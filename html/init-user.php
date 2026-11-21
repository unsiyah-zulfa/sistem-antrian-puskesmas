<?php  ?>
<script>
  window.adminHMDUser = {
    name: "<?= htmlspecialchars($_SESSION['nama'] ?? 'Admin') ?>",
    workspace: "Active Workspace",
    avatar: "../assets/images/avatar/my.jpg"
  };
</script>