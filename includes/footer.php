  <footer class="app-footer">
    <div class="row">
      <div class="col-xs-12">
        <div class="footer-copyright">Copyright &copy; <?php echo date('Y');?> <a href="http://www.viaviweb.com" target="_blank">Viaviweb.com</a>. All Rights Reserved.</div>
      </div>
    </div>
  </footer>

</div>
</div>

<script type="text/javascript" src="assets/js/vendor.js"></script> 
<script type="text/javascript" src="assets/js/app.js"></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script src="assets/js/notify.min.js"></script>

<script type="text/javascript" src="vendor/duDialog-master/duDialog.min.js"></script>

<script src="vendor/snackbar-master/snackbar.js"></script>

<script>

  $(document).ready(function(event) {

    $(document).on("click", ".enable_disable", function(e) {
      var _action;

      var _currentElement = $(this);
      var _id = $(this).data("id");
      var _table = $(this).data("table");
      var _column = $(this).data("column");

      var _for = $(this).prop("checked");
      if (_for == false) {
        _action = "disable";
      } else {
        _action = "enable";
      }

      $.ajax({
        type: 'post',
        url: 'processData.php',
        dataType: 'json',
        data: {id: _id, for_action: _action, table: _table, column: _column,'action':'toggle_status'},
        success: function(res) {
          Snackbar.show({text: res.msg, msgClass: 'success'});
        }
      });
    });
  })

  $("#checkall").click(function () {
    $("input:checkbox[name='wall_ids[]']").not(this).prop('checked', this.checked);
  });
</script>

<script type="text/javascript">
  function render_upload_image(input, target) {

    if (input.files && input.files[0]) {
      var reader = new FileReader();

      reader.onload = function(e) {
        target.attr('src', e.target.result);
      }

      reader.readAsDataURL(input.files[0]);
    }
  }

  function isImage(filename) {
    var ext = getExtension(filename);
    switch (ext.toLowerCase()) {
      case 'jpg':
      case 'jpeg':
      case 'png':
      case 'svg':
      case 'gif':
      return true;
    }
    return false;
  }

  function getExtension(filename) {
    var parts = filename.split('.');
    return parts[parts.length - 1];
  }
</script>

<?php 
if(isset($_SESSION['msg'])){ 

  $_class=($_SESSION["class"]) ? $_SESSION["class"] : "success";
  ?>
  <script type="text/javascript">

    var _msg="<?php echo $client_lang[$_SESSION["msg"]]; ?>";
    _msg=_msg.replace(/(<([^>]+)>)/ig,"");

    Snackbar.show({text: _msg, msgClass: '<?=$_class?>'});
  </script>
  <?php
  unset($_SESSION['msg']);
  unset($_SESSION['class']);
}
?>
</body>
</html>