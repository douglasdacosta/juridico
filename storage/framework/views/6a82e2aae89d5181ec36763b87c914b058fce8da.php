<div <?php echo e($attributes->merge(['class' => $makeBoxClass()])); ?>>

    
    <?php if(isset($icon)): ?>
        <span class="<?php echo e($makeIconClass()); ?>">
            <i class="<?php echo e($icon); ?>"></i>
        </span>
    <?php endif; ?>

    
    <div class="info-box-content">

        
        <?php if(isset($title)): ?>
            <span class="info-box-text">

                <?php if(isset($url) && $urlTarget == 'title'): ?>
                    <a class="info-box-url text-reset" href="<?php echo e($url); ?>">
                        <u><?php echo e($title); ?></u>
                    </a>
                <?php else: ?>
                    <?php echo e($title); ?>

                <?php endif; ?>

            </span>
        <?php endif; ?>

        
        <?php if(isset($text)): ?>
            <span class="info-box-number">

                <?php if(isset($url) && $urlTarget == 'text'): ?>
                    <a class="info-box-url text-reset" href="<?php echo e($url); ?>">
                        <u><?php echo e($text); ?></u>
                    </a>
                <?php else: ?>
                    <?php echo e($text); ?>

                <?php endif; ?>

            </span>
        <?php endif; ?>

        
        <?php if(isset($progress) && isset($attributes['id'])): ?>
            <?php if (isset($component)) { $__componentOriginal2833f5cd2f7c3d9d4665a747bd423726b7e93ccb = $component; } ?>
<?php $component = JeroenNoten\LaravelAdminLte\View\Components\Widget\Progress::resolve(['value' => ''.e($progress).'','theme' => ''.e($progressTheme).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('adminlte-progress'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(JeroenNoten\LaravelAdminLte\View\Components\Widget\Progress::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'progress-'.e($attributes['id']).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2833f5cd2f7c3d9d4665a747bd423726b7e93ccb)): ?>
<?php $component = $__componentOriginal2833f5cd2f7c3d9d4665a747bd423726b7e93ccb; ?>
<?php unset($__componentOriginal2833f5cd2f7c3d9d4665a747bd423726b7e93ccb); ?>
<?php endif; ?>
        <?php elseif(isset($progress)): ?>
            <?php if (isset($component)) { $__componentOriginal2833f5cd2f7c3d9d4665a747bd423726b7e93ccb = $component; } ?>
<?php $component = JeroenNoten\LaravelAdminLte\View\Components\Widget\Progress::resolve(['value' => ''.e($progress).'','theme' => ''.e($progressTheme).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('adminlte-progress'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(JeroenNoten\LaravelAdminLte\View\Components\Widget\Progress::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2833f5cd2f7c3d9d4665a747bd423726b7e93ccb)): ?>
<?php $component = $__componentOriginal2833f5cd2f7c3d9d4665a747bd423726b7e93ccb; ?>
<?php unset($__componentOriginal2833f5cd2f7c3d9d4665a747bd423726b7e93ccb); ?>
<?php endif; ?>
        <?php endif; ?>

        
        <?php if(isset($description)): ?>
            <span class="progress-description"><?php echo e($description); ?></span>
        <?php endif; ?>

    </div>

</div>



<?php if (! $__env->hasRenderedOnce('bc065351-2ee3-47cf-b20b-fbb2aab17f0d')): $__env->markAsRenderedOnce('bc065351-2ee3-47cf-b20b-fbb2aab17f0d'); ?>
<?php $__env->startPush('js'); ?>
<script>

    class _AdminLTE_InfoBox {

        /**
         * Constructor.
         *
         * target: The id of the target info box.
         */
        constructor(target)
        {
            this.target = target;
        }

        /**
         * Update the info box.
         *
         * data: An object with the new data.
         */
        update(data)
        {
            // Check if target and data exists.

            let t = $(`#${this.target}`);

            if (t.length <= 0 || ! data) {
                return;
            }

            // Update available data.

            if (data.title) {
                t.find('.info-box-text').html(data.title);
            }

            if (data.text) {
                t.find('.info-box-number').html(data.text);
            }

            if (data.icon) {
                t.find('.info-box-icon i').attr('class', data.icon);
            }

            if (data.description) {
                t.find('.progress-description').html(data.description);
            }

            if (data.url) {
                t.find('.info-box-url').attr('href', data.url);
            }

            // Update progress bar.

            if (data.progress) {
                let pBar = new _AdminLTE_Progress(`progress-${this.target}`);
                pBar.setValue(data.progress);
            }
        }
    }

</script>
<?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH C:\juridico\vendor\jeroennoten\laravel-adminlte\resources\views\components\widget\info-box.blade.php ENDPATH**/ ?>