<header class="main-header">
  <nav class="navbar navbar-static-top" style="background-color:#717A83; width:100%;">
    <div style="display: flex; align-items: center; justify-content: space-between; width:100%; padding: 5px 20px; background-color:#717A83;">

      <!-- Left: Brand -->
      <div class="navbar-header" style="display: flex; align-items: center;">
        <a href="#" class="navbar-brand" style="color:black; font-size: 22px; font-family: Times; white-space: nowrap;">
          <b>ONLINE VOTING SYSTEM</b>
        </a>
        <button type="button" class="navbar-toggle collapsed" style="background-color:#717A83; margin-left: 10px;" data-toggle="collapse" data-target="#navbar-collapse">
          <i class="fa fa-bars"></i>
        </button>
      </div>

      <!-- Center: Nav Links -->
      <div class="collapse navbar-collapse pull-left" id="navbar-collapse" style="display: flex; justify-content: center;">
        <ul class="nav navbar-nav" style="display: flex; gap: 20px;">
          <?php
            if(isset($_SESSION['student'])){
              echo "
                <li><a href='index.php'>HOME</a></li>
                <li><a href='transaction.php'>TRANSACTION</a></li>
              ";
            } 
          ?>
        </ul>
      </div>

      <!-- Right: User Menu -->
      <div class="navbar-custom-menu" style="display: flex; align-items: center; gap: 15px;">
        <ul class="nav navbar-nav" style="display: flex; align-items: center; gap: 15px;">
          <li class="user user-menu">
            <a href="#" style="display: flex; align-items: center; gap: 10px;">
              <div class="user-image-wrapper">
                <img src="<?php echo (!empty($voter['photo'])) ? 'images/'.$voter['photo'] : 'images/profile.jpg'; ?>" 
                     class="user-image" alt="User Image">
              </div>
              <span class="hidden-xs" style="color:black; font-size: 22px; font-family: Times; white-space: nowrap;">
                <?php echo $voter['firstname'].' '.$voter['lastname']; ?>
              </span>
            </a>
          </li>
          <li>
            <a href="logout.php" style="display: flex; align-items: center; gap: 5px;">
              <i class="fa fa-sign-out" style="color:black; font-size:22px;"></i>
              <b style="color:black; font-size: 22px; font-family: Times; white-space: nowrap;"> LOGOUT </b>
            </a>
          </li>  
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hover effect CSS -->
  <style>
    body {
      margin: 0;
      background-color: #717A83;
    }

    /* Navbar adjusts dynamically */
    .navbar {
      background-color: #717A83;
      display: flex;
      align-items: center;
      transition: all 0.3s ease;
      min-height: 100px; /* enough for hover image growth */
    }

    /* Image wrapper */
    .user-image-wrapper {
      display: inline-block;
      transition: all 0.3s ease;
    }

    /* Base user image size */
    .user-image {
      width: 80px !important;
      height: 80px !important;
      border-radius: 50%;
      transition: all 0.3s ease;
      display: block;
    }

    /* Hover effect */
    .user.user-menu:hover .user-image-wrapper {
      width: 160px;
      height: 160px;
    }

    .user.user-menu:hover .user-image {
      width: 160px !important;
      height: 160px !important;
    }

    /* Vertically center text next to image */
    .user.user-menu a {
      display: flex;
      align-items: center;
    }

    /* Override Bootstrap navbar img constraints */
    .navbar-nav > li.user-menu > a img {
      max-height: none !important;
    }
  </style>
</header>
