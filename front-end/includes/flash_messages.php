<?php
/**
 * ============================================================
 * Petfy – FRONT-END: Exibição de Mensagens Flash
 * ============================================================
 */

$flash = function_exists('getFlashMessage') ? getFlashMessage() : (isset($_SESSION['flash']) ? $_SESSION['flash'] : null);
if (isset($_SESSION['flash']) && !function_exists('getFlashMessage')) {
    unset($_SESSION['flash']);
}

if ($flash): 
    $type = $flash['type'] ?? 'info';
    $message = $flash['message'] ?? '';
    
    $icon = 'fa-info-circle';
    $alertClass = 'alert-info';
    
    if ($type === 'success') {
        $icon = 'fa-check-circle';
        $alertClass = 'alert-success';
    } elseif ($type === 'error') {
        $icon = 'fa-exclamation-triangle';
        $alertClass = 'alert-error';
    } elseif ($type === 'warning') {
        $icon = 'fa-exclamation-circle';
        $alertClass = 'alert-warning';
    }
?>
<div class="flash-alert-container">
    <div class="flash-alert <?= $alertClass ?>">
        <i class="fas <?= $icon ?>"></i>
        <div class="flash-text"><?= $message ?></div>
        <button type="button" class="flash-close" onclick="this.parentElement.remove()">×</button>
    </div>
</div>
<?php endif; ?>
