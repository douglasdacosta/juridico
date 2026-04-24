<?php

namespace App\Services;

// use Google\Client;
use Google\Client as GoogleClientLib;
use Google\Service\Drive as GoogleDrive;
use App\Services\GoogleClient;
use Google\Service\Drive;
use Illuminate\Support\Facades\Storage;

class GoogleDriveService
{
    protected $client;
    protected $service;

    protected $folderId = '1oEnFTfnE2_VlYkilI4ULLzny8rX9Y3sh'; // ID da pasta no Google Drive

    // public function __construct()
    // {
    //     $this->client = new Client();
    //     $this->client->setAuthConfig(config('filesystems.disks.google.credentials_file', storage_path('app/google/credentials.json')));
    //     $this->client->addScope(Drive::DRIVE);
    //     $this->client->setAccessType('offline');

    //     $this->service = new Drive($this->client);
    //     $this->folderId = env('GOOGLE_DRIVE_FOLDER_ID');
    // }

    public function __construct()
    {
        $client = new GoogleClientLib();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setAccessType('offline');

        // Pega token salvo
        if (!Storage::exists('google_tokens.json')) {
            throw new \Exception('Arquivo google_tokens.json não encontrado.');
        }

        $token = json_decode(Storage::get('google_tokens.json'), true);
        $client->setAccessToken($token);

        // Se expirou, renova
        if ($client->isAccessTokenExpired()) {
            $refreshToken = $token['refresh_token'] ?? null;

            if ($refreshToken) {
                // Usa o refresh token para obter um novo access token
                $client->fetchAccessTokenWithRefreshToken($refreshToken);

                // Recupera o novo token atualizado
                $newToken = $client->getAccessToken();

                // Garante que o refresh_token não se perca
                $newToken['refresh_token'] = $refreshToken;

                // Salva o token atualizado
                Storage::put('google_tokens.json', json_encode($newToken));
            } else {
                throw new \Exception('Refresh token ausente no arquivo google_tokens.json.');
            }
        }

        // Define o escopo de acesso ao Google Drive
        $client->addScope(GoogleDrive::DRIVE);

        // Inicializa o serviço do Google Drive
        $this->service = new GoogleDrive($client);
    }


    public function listFiles($query = null)
    {

        $params = [
            'pageSize' => 50,
            'fields' => 'nextPageToken, files(id, name, mimeType, size)',
            'q' => sprintf("'%s' in parents and trashed = false", $this->folderId),
        ];

        if ($query) {
            $params['q'] .= sprintf(" and name contains '%s'", addslashes($query));
        }

        $results = $this->service->files->listFiles($params);

        $files = collect($results->getFiles())->map(function ($file) {
            return [
                'id' => $file->getId(),
                'name' => $file->getName(),
                'mimeType' => $file->getMimeType(),
                'size' => $file->getSize(),
            ];
        });

        // info(['query' => $params['q'], 'files' => $files]);

        return $files;
    }


    /**
     * Enviar arquivo para o Drive
     */
    public function uploadFile($filePath, $fileName = null, $mimeType = null)
    {
        $fileMetadata = new Drive\DriveFile([
            'name' => $fileName ?? basename($filePath),
            'parents' => [$this->folderId],
        ]);

        if ($this->folderId) {
            $fileMetadata->setParents([$this->folderId]);
        }

        $content = file_get_contents($filePath);

        $file = $this->service->files->create(
            $fileMetadata,
            [
                'data' => $content,
                'mimeType' => $mimeType ?? mime_content_type($filePath),
                'uploadType' => 'multipart',
                'fields' => 'id, name'
            ]
        );

        return $file;
    }

    public function downloadFile($id)
    {
        try {
            $file = $this->service->files->get($id, ['fields' => 'mimeType, name']);
            $mimeType = $file->getMimeType();
            $name = $file->getName();

            $googleDocsTypes = [
                'application/vnd.google-apps.document' => 'application/pdf',
                'application/vnd.google-apps.spreadsheet' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.google-apps.presentation' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            ];

            if (isset($googleDocsTypes[$mimeType])) {
                $response = $this->service->files->export($id, $googleDocsTypes[$mimeType], ['alt' => 'media']);
            } else {
                $response = $this->service->files->get($id, ['alt' => 'media']);
            }

            $content = $response->getBody()->getContents();

            return response($content, 200)
                ->header('Content-Type', 'application/octet-stream')
                ->header('Content-Disposition', 'attachment; filename="' . $name . '"');
        } catch (\Exception $e) {
            info('Erro ao baixar arquivo: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao baixar arquivo.'], 500);
        }
    }

    /**
     * Criar pasta no Drive
     */
    public function createFolder($name, $parentId = null)
    {
        $folderMetadata = new \Google\Service\Drive\DriveFile([
            'name' => $name,
            'mimeType' => 'application/vnd.google-apps.folder',
        ]);

        // Se quiser criar dentro de uma pasta específica
        if ($parentId) {
            $folderMetadata->setParents([$parentId]);
        }

        $folder = $this->service->files->create($folderMetadata, [
            'fields' => 'id, name, parents'
        ]);

        return $folder;
    }


    /**
     *  Deletar arquivo
     */
    public function deleteFile($fileId)
    {
        return $this->service->files->delete($fileId);
    }
}
