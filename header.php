<!-- <?php include('database.php');
if(isset($_SESSION['email'])) {
$email=$_SESSION['email'];
$checkUsername= "SELECT *  FROM users inner join roles on users.role_id = roles.role_id WHERE email='$email'";
$checkResult= mysqli_query($db, $checkUsername);
$result= mysqli_fetch_assoc($checkResult);
$first_name=$result['first_name'];
$last_name=$result['last_name'];
$role_name=$result['role_name'];
$user_id=$result['user_id'];
}
?>

<style>
  .overlay-image {
    top:0;
    z-index: 2;
    transition: transform 0.3s ease;

  }
  .overlay-image:hover {
    transform: scale(1.2);
  }
</style>
<div class="w3-col w3-hide-small w3-theme-l3" style="width:100%;height: 20vh !important;">
  <div class="w3-s1">
    <img src="assets/img/fit.png" alt="Overlay Image" class="w3-col s1" style="height:20vh;width:30vh">
  </div>
  <div class="w3-rest">
    <img src="assets/img/f.png" alt="Overlay Image" class="w3-col s1 overlay-image icons" style="height:5vh;width:5vh">
    <img src="assets/img/i.png" alt="Overlay Image" class="w3-col s1 overlay-image icons" style="height:5vh;width:5vh">
    <img src="assets/img/t.png" alt="Overlay Image" class="w3-col s1 overlay-image icons" style="height:5vh;width:5vh">
    <img src="assets/img/m.png" alt="Overlay Image" class="w3-col s1 overlay-image icons" style="height:5vh;width:5vh">
    <img src="assets/img/a.png" alt="Overlay Image" class="w3-col s1 overlay-image icons" style="height:5vh;width:5vh">
    <img src="assets/img/n.png" alt="Overlay Image" class="w3-col s1 overlay-image icons" style="height:5vh;width:5vh">
    <div class="w3-col">
      <span class="h1">THE NORTHERN MIGHT</span>
      <i class="w3-col">Ugad, Cabagan, Isabela</i>
    </div>
  </div>

</div>
<div class="w3-col w3-hide-medium w3-hide-large w3-theme-l3" style="width:100%;height: 5vh !important;">
  <img src="assets/img/fit.png" alt="Overlay Image" class="w3-col s1" style="height:5vh;width:10vh">
  <span class="w3-rest h1">THE NORTHERN MIGHT</span>
</div> -->
