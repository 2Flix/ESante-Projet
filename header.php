<header>
  <nav style="background-color:#333; padding:10px; display:flex; align-items:center; justify-content:space-between;">
    <div>
      <a href="visualiser.php" style="color:white; margin-right:20px; text-decoration:none; font-weight:bold;">Visualiser</a>
      <a href="analyse.php" style="color:white; margin-right:20px; text-decoration:none; font-weight:bold;">Analyse</a>
      <a href="traitement.php" style="color:white; text-decoration:none; font-weight:bold;">Traitement</a>
    </div>
    <form action="upload_handler.php" method="POST" enctype="multipart/form-data" style="margin:0;">
      <label style="cursor: pointer; color:white; font-weight: bold;">
        Téléverser une image
        <input type="file" name="image" onchange="this.form.submit()" hidden />
      </label>
    </form>
  </nav>
</header>

