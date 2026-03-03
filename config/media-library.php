<?php

return [
    /*
     * O disco onde os arquivos serão armazenados.
     */
    'disk_name' => env('MEDIA_DISK', 'public'),

    /*
     * O máximo de tamanho de arquivo em bytes para upload.
     * 10MB default
     */
    'max_file_size' => 1024 * 1024 * 10,

    /*
     * Esta fila será usada para processar jobs de mídia.
     */
    'queue_name' => env('MEDIA_QUEUE', 'media'),

    /*
     * O modelo de mídia a ser usado.
     */
    'media_model' => Spatie\MediaLibrary\MediaCollections\Models\Media::class,

    /*
     * Habilita conversões de mídia.
     */
    'enable_video_uploads' => false,

    /*
     * Tempo de vida do temporary URL (para S3)
     */
    'temporary_upload_max_age' => 3600,

    /*
     * Quando usar a manipulação de imagens via GD ou Imagick
     */
    'image_driver' => env('IMAGE_DRIVER', 'gd'),

    /*
     * Jobs para processamento em fila
     */
    'jobs' => [
        'perform_conversions' => \Spatie\MediaLibrary\Conversions\Jobs\PerformConversionsJob::class,
        'generate_responsive_images' => \Spatie\MediaLibrary\ResponsiveImages\Jobs\GenerateResponsiveImagesJob::class,
    ],

    /*
     * Gerador de nomes de arquivo
     */
    'file_namer' => \Spatie\MediaLibrary\Support\FileNamer\DefaultFileNamer::class,

    /*
     * Caminho para armazenar arquivos temporários
     */
    'temporary_directory_path' => storage_path('app/livewire-tmp'),

    /*
     * Conversões de imagens responsivas
     */
    'responsive_images' => [
        'width_calculator' => \Spatie\MediaLibrary\ResponsiveImages\WidthCalculator\FileSizeOptimizedWidthCalculator::class,
        'use_tiny_placeholders' => true,
        'tiny_placeholder_generator' => \Spatie\MediaLibrary\ResponsiveImages\TinyPlaceholderGenerator\Blurred::class,
    ],

    /*
     * Conversões automáticas
     */
    'auto_generate_conversions' => env('MEDIA_AUTO_GENERATE_CONVERSIONS', true),

    /*
     * Otimizadores de imagem
     */
    'image_optimizers' => [
        \Spatie\ImageOptimizer\Optimizers\Jpegoptim::class => [
            '-m85',
            '--strip-all',
            '--all-progressive',
        ],
        \Spatie\ImageOptimizer\Optimizers\Pngquant::class => [
            '--force',
            '--quality=65-80',
        ],
        \Spatie\ImageOptimizer\Optimizers\Optipng::class => [
            '-i0',
            '-o2',
            '-quiet',
        ],
        \Spatie\ImageOptimizer\Optimizers\Svgo::class => [
            '--disable=cleanupIDs',
        ],
        \Spatie\ImageOptimizer\Optimizers\Gifsicle::class => [
            '-b',
            '-O3',
        ],
        \Spatie\ImageOptimizer\Optimizers\Cwebp::class => [
            '-m 6',
            '-pass 10',
            '-mt',
            '-q 80',
        ],
    ],

    /*
     * Gerador de nomes de conversões
     */
    'conversion_file_namer' => \Spatie\MediaLibrary\Conversions\DefaultConversionFileNamer::class,

    /*
     * Gerador de caminho de mídia
     */
    'path_generator' => \App\Domains\Media\Services\CustomPathGenerator::class,

    /*
     * Gerador de URL
     */
    'url_generator' => \Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator::class,

    /*
     * Versionamento de mídia
     */
    'version_urls' => false,

    /*
     * Modelo de mídia para coleções
     */
    'media_model' => \Spatie\MediaLibrary\MediaCollections\Models\Media::class,

    /*
     * Remover mídia quando o modelo pai for deletado
     */
    'delete_media_on_model_deletion' => true,
];