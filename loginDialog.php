<!DOCTYPE html>
<div style='height:200px;'>    
</div>
<div class="row" >
    <div class="col-sm-4"></div>
    <div class="col-sm4" style="background-color: #EAEA7F;padding: 30px;border-radius: 15px;">      
        <table>
            <tr>
                <td width="150px">Felhasználó:</td>
                <td width="250px"><input type='email' name='username'></td>
            </tr>            
            <tr>
                <td>Jelszó:</td>
                <td><input type='password' name='password'></td>
            </tr>        
        </table>
        <br>
        <input type='submit' style="" id="login">       
        <?php 
                if ($badUserPass) echo "<span style='color:red;'>Rossz felhasználónév vagy jelszó!</span>";
                $badUserPass=false;
        ?>
    </div>
    <div class="col-sm-4"></div>
</div>

