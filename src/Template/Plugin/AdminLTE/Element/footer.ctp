<footer class="main-footer">
  <?php if (isset($layout) && $layout == 'top'): ?>
  <div class="container">
  <?php endif; ?>
    <div class="pull-right hidden-xs">
      <a href="/">Italia S.r.l.</a>
    </div>
    <strong>Copyright &copy; <?php echo date('Y');?> <a href="/">Italia S.r.l.</a></strong> - Tutti i diritti riservati.
  <?php if (isset($layout) && $layout == 'top'): ?>
  </div>
  <?php endif; ?>
</footer>