<header>
  <form action="upload_handler.php" method="POST" enctype="multipart/form-data">
    <label style="cursor: pointer; font-weight: bold;">
      Téléverser une image
      <input type="file" name="image" onchange="this.form.submit()" hidden />
    </label>
  </form>
</header>
