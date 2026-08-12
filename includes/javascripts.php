<?php
/** @var string $strStartDate */
$webbyStartDate = $strStartDate ?? '2012-12-31';
?>
<script type="text/javascript">
window.webbycms = {
    startDate: '<?= e($webbyStartDate) ?>',
    baseUrl: '<?= e(url('/')) ?>'
};
</script>
