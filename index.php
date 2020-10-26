<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="icon" href="http://faviconka.ru/ico/faviconka_ru_1009.png" type="image/x-icon">
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<link href="css/style.css" rel="stylesheet" id="style-css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css"/>
<link href="https://fonts.googleapis.com/css2?family=Rubik&display=swap" rel="stylesheet">
<title>Ticket system</title>
</head>
<body>
	<?php
	require 'db.php';
    $data = $_POST;
    $id = $_SESSION['logged_user']->id;
    $email = $_SESSION['logged_user']->email;
    $right = $_SESSION['logged_user']->right;
    date_default_timezone_set("Europe/Moscow");
    $date = date("d.m.Y H:i");
	if ( isset($data['submit']) )
	{
		$user = R::findOne('users', 'email = ?', array($data['email']));
		if ( $user )
		{
		
			if ( password_verify($data['password'], $user->password) )
			{
			
        $_SESSION['logged_user'] = $user;
        $_POST = NULL;
        header('Location: index.php');
        exit;
			}else
			{
				$errors[] = 'Invalid password!';
			}

		}else
		{
			$errors[] = 'The user with this username was not found!';
		}
		
		if ( ! empty($errors) )
		{
			echo '<div id="errors" style="color:red;">' .array_shift($errors). '</div><hr>';
		}

  }
  if ( isset($data['ticketsubmit']) )
	{
    $rep = R::dispense('reports');
    $rep->fromid = $id;
    $rep->status = "Open";
    $rep->theme = $data['theme'];
    $rep->priority = $data['priority'];
    $rep->department = $data['department'];
    $rep->email = $email;
    $rep->date = $date;
    $rep->disc = $data['problem'];
    R::store($rep);
    $_POST = NULL;
    header('Location: index.php');
    exit;
  }
  if ( isset($data['delete']) )
	{
    $delid = $data ['deletecopy'];
    $sql = R::exec("DELETE FROM reports WHERE id = $delid"); 
    $_POST = NULL;
    header('Location: index.php');
    exit;
  }

  if ( isset($data['save']) )
	{
    $inid = $data ['copyid'];
    $newstatus = $data['status'];
    $savesol= $data['savesol'];
    $sql = R::exec("UPDATE reports SET status = '$newstatus' WHERE id = $inid"); 
    $sql2 = R::exec("UPDATE reports SET solution = '$savesol' WHERE id = $inid"); 
    $_POST = NULL;
    header('Location: index.php');
    exit;
  }
 ?>
<?php 
    if ( isset ($_SESSION['logged_user']) ) : ?>
    <div class="container mainw">                
	<div class="row">
		<div class="col-md-12">
			<div class="heading">
			<h6 class="text-left"><a href = "index.php"><img src="images/logo.png" /></a>Welcome, <?php echo $_SESSION['logged_user']->email;?> <a class= "btn btn-outline-primary btn-sm" href="logout.php">Exit</a> </h6>
      </div>
      
		</div>	
	</div>
	<div class="list">
		<div class="row">
					<div class="col-md-12">
                        <div class="header" id="parse1">                                
 <!-- Modal -->
 <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">New ticket</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form class="seminor-login-form" method="POST" name="1" action="">
    <div class="form-group">
    <label class="form-control-placeholder">Theme</label>
      <input type="text" id = "theme" name ="theme" class="form-control" required autocomplete="off">
    </div>
    <div class="form-group">
    <label class="form-control-placeholder">Priority</label>
    <select class="form-control" id = "priority" name ="priority" required >
       <option>Low</option>
       <option>Medium</option>
       <option>High</option>
       <option>Critical</option>
       <option>Emergency</option>
     </select>
    </div>
    <div class="form-group">
    <label class="select-form-control-placeholder" for="sel1">Department</label>
     <select class="form-control" id = "department" name ="department" required >
       <option>Support</option>
       <option>IT (Tech) Department</option>
       <option>Finance department</option>
       <option>Overall issue</option>
       <option>Others</option>
     </select>
    </div>
    <div class="form-group">
      <label class="form-control-placeholder">Problem</label>
      <textarea class="form-control"  id = "problem" name ="problem" required></textarea>
    </div>
        </div>
        <div class="modal-footer">
        <button id="ticketsubmit" type="submit" name="ticketsubmit" class="btn btn-success">Create</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>                        
    </form>
<div class="container">
<div class="messaging">
      <div class="im">
        <div class="ipe">
          <div class="headind_srch">
              <div class="row">
            <div class="recent_heading">
              <h4><?php if ($right == 0){echo 'My tickets';}else{echo 'All open tickets (admin)';}?></h4>
            </div>
            <div class="bar">
            <?php if ($right == 0){echo 
          '<button class="btn btn-success" data-toggle="modal" data-target="#exampleModalCenter" type="submit">New Ticket</button>';} ?>
            </div>
        </div>
          </div>
          <div class="ich">

          <div id="listId">
  <ul class="list">
          <?php 
if ($right==0){
$result = R::getAll( "SELECT * FROM reports WHERE fromid = '$id' ORDER BY id DESC" );}
if ($right==1){
$result = R::getAll( "SELECT * FROM reports WHERE status = 'Open' ORDER BY id DESC" );}
foreach( $result as $info ) {
  ?>       
 <div class="modal fade" id="delmodal" tabindex="-1" role="dialog" aria-labelledby="delmodal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="delmodal">Delete ticket</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
        Are you sure you want to delete the ticket?
        </div>
        <div class="modal-footer">
        <form action="" method="post"><input type="hidden" name= "deletecopy" readonly value="<?php echo $info ['id']; ?>"> <button class="btn btn-danger" id="delete" name="delete" type="submit">Delete</button></form>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
            <div class="cl">
              <div class="ch">
                <div class="ib">
                <div class="row">
                <div class="col-md-4"> <span>ID: <?php echo $info ['id']; ?></span>
                <?php if($right==0 and $info['status']==Open){ echo
                '<button class="btn btn-danger" id="delmodal" name="delmodal" data-toggle="modal" data-target="#delmodal" >Delete</button>';}
                elseif ($right==1){echo '<button class="btn btn-danger" id="delmodal" name="delmodal" data-toggle="modal" data-target="#delmodal" >Delete</button>';}?>
                </div>
                <div class="col-md-4"> Priority: <?php echo $info ['priority'];?></span>
              </div>
              <div class="col-md-4"><span><?php echo $info ['date']; ?></span></div>
                </div>
               <div class="row">
                  <div class="col-md-4"> 
                    <span><?php echo $info ['date'];?></span>
                    </div>
                    <div class="col-md-4">   
                    <?php if ($right==1): ?>
                      <form action="" method="post">
                    <span>Status: <label class="radio-inline"><input type="radio" id="status" name="status" value="Open" <?php if ($info['status']==Open) echo 'checked="checked"'; ?>> Open</label>
						        <label class="radio-inline"><input type="radio" id="status" name="status" value="Close" <?php if ($info['status']==Close) echo 'checked="checked"'; ?>> Close</label>
                    <label class="radio-inline"><input type="radio" id="status" name="status" value="In_Progress" <?php if ($info['status']==In_Progress) echo 'checked="checked"'; ?>> In progress</label>
                    <?php else : ?> 
                  <span><p>Status: <?php echo $info ['status'];?></p></span>
                  <?php endif; ?> 
                  </span>
                    </div>
                    <div class="col-md-4">
                  </div>
                </div>
                  <hr>
                  <div class="row"><div class="col-md-12"><span><p>Author: <?php echo $info ['email'];?></p></span>
                  <div class="row">
                  <div class="col-md-12"> 
                <span><p>Problem: <?php echo $info ['disc'];?></p></span>
                </div>
                </div><hr>
                <div class="row">
                  <div class="col-md-12"> 
                  <?php if ($right==1): ?>
                <span>Solution: <br><textarea class="form-control" name="savesol" id="status" required><?php echo $info ['solution'];?></textarea></span>
                <?php else : ?> 
                  <span><p>Solution: <?php echo $info ['solution'];?></p></span>
                  <?php endif; ?>
              </div>
                </div>                    <?php if ($right==1): ?>
                  <input type="hidden" name= "copyid" readonly value="<?php echo $info ['id']; ?>">
                    <button class="col-md-12 btn btn-primary" id="save" name="save" type="submit">Save</button>
                  </form>
                    <?php else : ?> <?php endif; ?>
                </div>
              </div><hr class="hr-washed">
            </div>
          <?php } ?>
          </ul>
          <ul class="pag pagination"></ul>
          <div class="text-center"><?php if ($info ['id']==null and $right==0) {echo "You haven't created any tickets";}?></div>
</div>
		  </div>
        </div>
      </div>
    <br>                 
    </div></div>
                        </div>
					</div>
                </div>	
            </div>
	</div>	
    <?php else : ?>
<div class="container mainw">
	<div class="row">
		<div class="col-md-12">
			<div class="heading">				
				<img src="images/logo.png" />
				Ticket system
      </div>
		</div>	
	</div>
	<div class="list">
		<div class="row">
					<div class="col-md-12">
                        <div class="header" id="parse1">							
							<form class="form-signin" method = "POST">
								<div class="text-center mb-4">
								  <h1 class="h3 mb-3 font-weight-normal">Login</h1>
								</div>
								<div class="form-label-group">
								  <input name ="email" type="email" class="form-control" placeholder="Email address" required autofocus>		  
								</div><br>
								<div class="form-label-group">
								  <input name = "password" type="password" class="form-control" placeholder="Password" required>
								</div>
						  
								<div class="checkbox mb-3">
								</div>		
								<div class="text-center">		
								<button name = "submit" class="btn-lg btn-success" type="submit">Sign in</button>
								</div>
							  </form>
                        </div>
                        <div class="main" id="main">
                        </div>
					</div>
                </div>	
            </div>
	</div>	
<?php endif; ?>
<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/list.js/1.5.0/list.min.js"></script>
<script>
  var options = {
    valueNames: [ 'theme' ],
    page: 4,
    pagination: true
  };
  var listObj = new List('listId', options);
</script>
</body>
</html>