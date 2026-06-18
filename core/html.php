<?php

function render(string $filepath, array $data = []): string
{
    if (!is_file($filepath)) {
        $view = $filepath;
        $filepath = __DIR__ . '/../app/views/' . $view . '.php';

        if (!is_file($filepath) && str_starts_with($view, 'admin/')) {
            $filepath = __DIR__ . '/../app/admin/views/' . substr($view, 6) . '.php';
        }
    }

    if (!is_file($filepath)) {
        throw new RuntimeException('View not found: ' . $filepath);
    }

    extract($data, EXTR_SKIP);

    ob_start();
    require $filepath;
    return ob_get_clean();
}

function render_in_layout(string $view, string $layout, array $data = []): string
{
    $content = render($view, $data);
    $data['content'] = $content;

    return render($layout, $data);
}

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
