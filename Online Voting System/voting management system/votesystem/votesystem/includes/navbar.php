<header class="main-header">
  <nav class="navbar navbar-static-top custom-navbar">
    <div class="navbar-flex-row">

      <!-- Left: Brand / Title -->
      <div class="navbar-brand-group">
        <a href="#" class="navbar-brand-custom">
          <b>ONLINE VOTING SYSTEM</b>
        </a>
      </div>

      <!-- Center: Nav Links -->
      <div class="navbar-links-group">
        <ul class="nav navbar-nav navbar-links-list">
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
      <div class="navbar-user-group">
        <ul class="nav navbar-nav navbar-user-list">
          <li class="user user-menu">
            <a href="#" class="user-link">
              <div class="user-image-wrapper">
                <img src="<?php echo (!empty($voter['photo'])) ? 'images/'.$voter['photo'] : 'images/profile.jpg'; ?>"
                     class="user-image" alt="User Image">
              </div>
              <span class="user-name">
                <?php echo $voter['firstname'].' '.$voter['lastname']; ?>
              </span>
            </a>
          </li>
          <li>
            <a href="logout.php" class="logout-btn">
              <i class="fa fa-sign-out"></i> LOGOUT
            </a>
          </li>
        </ul>
      </div>

    </div>
  </nav>

  <style>
    body{
      margin: 0;
    }

    /* ---- Force content-wrapper to match page background, not AdminLTE default ---- */
    .content-wrapper{
      background-color: #F1E9D2 !important;
    }

    /* ---- Force header to grow with content instead of clipping ---- */
    .main-header{
      height: auto !important;
      min-height: auto !important;
      overflow: visible !important;
    }

    .main-header .navbar{
      height: auto !important;
      min-height: auto !important;
      overflow: visible !important;
    }

    /* ---- Reset conflicting Bootstrap defaults ---- */
    .custom-navbar,
    .custom-navbar .navbar-header,
    .custom-navbar .navbar-nav,
    .custom-navbar .navbar-nav > li,
    .custom-navbar .navbar-brand{
      float: none;
      margin: 0;
      padding: 0;
      height: auto;
      line-height: normal;
    }

    /* ---- Force real static layout, no AdminLTE overlap ---- */
    .main-header,
    .main-header .navbar-custom-menu,
    .main-header .user-menu{
      position: static !important;
      float: none !important;
      top: auto !important;
      right: auto !important;
      transform: none !important;
    }

    /* ---- Outer navbar: gray background ---- */
    .custom-navbar{
      background-color: #717A83 !important;
      width: 100%;
      min-height: 100px;
      transition: all 0.3s ease;
    }

    /* ---- Main flex row: brand | links | user menu ---- */
    .navbar-flex-row{
      display: flex;
      align-items: center;
      justify-content: space-between;
      width: 100%;
      padding: 5px 20px;
      box-sizing: border-box;
      background-color: #717A83;
    }

    /* ---- Left: brand group ---- */
    .navbar-brand-group{
      display: flex;
      align-items: center;
    }

    .navbar-brand-custom{
      color: black;
      font-size: 22px;
      font-family: Times;
      white-space: nowrap;
      text-decoration: none;
    }

    /* ---- Center: nav links ---- */
    .navbar-links-group{
      display: flex;
      justify-content: center;
      align-items: center;
      flex: 1;
    }

    .navbar-links-list{
      display: flex;
      align-items: center;
      gap: 20px;
      margin: 0;
      padding: 0;
    }

    /* ---- Right: user menu group ---- */
    .navbar-user-group{
      display: flex;
      align-items: center;
    }

    .navbar-user-list{
      display: flex;
      align-items: center;
      gap: 15px;
      margin: 0;
      padding: 0;
    }

    .navbar-user-list > li{
      display: flex;
      align-items: center;
    }

    /* ---- User image + name: left to right, vertically centered ---- */
    .user-link{
      display: flex !important;
      flex-direction: row !important;
      align-items: center;
      justify-content: center;
      gap: 10px;
      text-decoration: none;
      height: 100%;
    }

    .user-image-wrapper{
      width: 80px;
      height: 80px;
      flex-shrink: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto;
    }

    .user-image{
      width: 80px !important;
      height: 80px !important;
      border-radius: 50%;
      display: block;
      object-fit: cover;
      object-position: center;
      transition: transform 0.3s ease;
      transform-origin: center;
    }

    .user-name{
      color: black;
      font-size: 22px;
      font-family: Times;
      white-space: nowrap;
      line-height: 1;
    }

    .user.user-menu:hover .user-image{
      transform: scale(2);
    }

    /* ---- Logout as an actual button ---- */
    .logout-btn{
      display: flex;
      align-items: center;
      gap: 8px;
      background-color: #d9534f;
      color: white !important;
      font-family: Times;
      font-size: 18px;
      font-weight: bold;
      padding: 8px 16px;
      border-radius: 5px;
      text-decoration: none;
      transition: background-color 0.2s ease;
      white-space: nowrap;
    }

    .logout-btn:hover{
      background-color: #c9302c;
      color: white !important;
      text-decoration: none;
    }

    /* ---- Responsive: stack top to bottom on small screens only ---- */
    @media (max-width: 767px){
      .navbar-flex-row{
        flex-direction: column;
        align-items: center;
        row-gap: 15px;
        padding: 15px 20px;
      }

      .navbar-brand-group,
      .navbar-links-group,
      .navbar-user-group{
        width: 100%;
        justify-content: center;
      }

      .navbar-links-list{
        flex-wrap: wrap;
        justify-content: center;
      }

      .navbar-user-list{
        flex-direction: column;
        row-gap: 12px;
      }

      .user-link{
        flex-direction: column !important;
        text-align: center;
      }

      .user.user-menu:hover .user-image{
        transform: none;
      }
    }
  </style>
</header>

<script>
(function(){
  var applying = false;

  // Only job now: keep main-header's real height in sync with the navbar's
  // actual rendered height, so normal document flow pushes content below it
  // correctly with NO manual margin needed.
  function syncHeaderHeight(){
    var mainHeader = document.querySelector('.main-header');
    var navbar = document.querySelector('.custom-navbar');

    if(!navbar || !mainHeader || applying) return;

    var realHeight = navbar.offsetHeight;

    applying = true;
    mainHeader.style.setProperty('height', realHeight + 'px', 'important');
    mainHeader.style.setProperty('min-height', realHeight + 'px', 'important');
    applying = false;
  }

  function init(){
    var navbar = document.querySelector('.custom-navbar');
    var mainHeader = document.querySelector('.main-header');

    if(navbar){
      var sizeObserver = new ResizeObserver(function(){
        syncHeaderHeight();
      });
      sizeObserver.observe(navbar);
    }

    if(mainHeader){
      var styleObserver = new MutationObserver(function(){
        if(!applying) syncHeaderHeight();
      });
      styleObserver.observe(mainHeader, { attributes: true, attributeFilter: ['style'] });
    }

    syncHeaderHeight();
  }

  document.addEventListener('DOMContentLoaded', init);
  window.addEventListener('load', syncHeaderHeight);
  setTimeout(syncHeaderHeight, 100);
  setTimeout(syncHeaderHeight, 500);
  setTimeout(syncHeaderHeight, 1000);
})();
</script>