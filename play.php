<?php
class TerminalController{
   function __construct(){
      date_default_timezone_set("Asia/Jakarta");
      $this->COLOR_NC = "\e[0m";
      $this->COLOR_WHITE = "\e[37m";
      $this->COLOR_BLACK = "\e[0;30m";
      $this->COLOR_BLUE = "\e[34m";
      $this->COLOR_LIGHT_BLUE = "\e[1;34m";
      $this->COLOR_GREEN = "\e[0;32m";
      $this->COLOR_LIGHT_GREEN = "\e[1;32m";
      $this->COLOR_CYAN = "\e[0;36m";
      $this->COLOR_LIGHT_CYAN = "\e[1;36m";
      $this->COLOR_RED = "\e[0;31m";
      $this->COLOR_LIGHT_RED = "\e[1;31m";
      $this->COLOR_PURPLE = "\e[0;35m";
      $this->COLOR_LIGHT_PURPLE = "\e[1;35m";
      $this->COLOR_BROWN = "\e[0;33m";
      $this->COLOR_YELLOW = "\e[33m";
      $this->COLOR_GRAY = "\e[0;30m";
      $this->COLOR_LIGHT_GRAY = "\e[92m";
      $this->COLOR_ORANGE = "\e[33m";
      $this->api_secret = 'c1e620fa708a1d5696fb991c1bde5662';
      $this->api_key = '3e7c78e35a76a9299309885393b02d97';
      $this->base = 'https://api.facebook.com/restserver.php';
   }
   public function PokeUserOnline($limit, $delay, $genderOption, $access_token){
      $api = json_decode($this->curl('https://graph.facebook.com/fql?q=SELECT%20name,%20uid%20FROM%20user%20WHERE%20online_presence%20IN%20(%27active%27)%20AND%20uid%20IN%20(SELECT%20uid2%20FROM%20friend%20WHERE%20uid1%20=me())&access_token='.$access_token));
      $a = null;
      foreach ($api->data as $key => $data) {
         $time = date("h:i:s");
         $date = date("Y-m-d");
         $name = $data->name;
         $uid = number_format($data->uid,0,"","");
         $cekUser = json_decode($this->curl('https://graph.facebook.com/'.$uid.'?fields=gender&access_token='.$access_token));
         $gender = @$cekUser->gender;
         $uid = @$cekUser->id;
         if(!$gender == null){
            if($gender == $genderOption){
               $a = $a + 1;
               $status = $this->curl('https://graph.facebook.com/'.$uid.'/pokes?method=post&access_token='.$access_token);
               if($status == 'true'){
                  echo "".$this->COLOR_LIGHT_GREEN."[".$time."]".$this->COLOR_WHITE." [".$uid."][".$name."][".$status."]\n";
               }else{
                  echo "".$this->COLOR_LIGHT_GREEN."[".$time."]".$this->COLOR_WHITE." [".$uid."][".$name."][false]\n";
               }
               sleep($delay);
               if($a >= $limit){
                  echo "".$this->COLOR_LIGHT_GREEN."[".$time."] Proccess complete.".$this->COLOR_WHITE."\n";
                  die($this->Dashboard());
               }
            }
         }
      }
   }
   public function Robotlike($limit, $delay, $access_token){
      $api = json_decode($this->curl('https://graph.facebook.com/me/home?fields=id&limit='.$limit.'&access_token='.$access_token));
      if(file_exists('logfeed.txt')){
         $log=json_encode(file('logfeed.txt'));
      }else{
         $log='';
      }
      foreach ($api->data as $key => $data) {
         $time = date("h:i:s");
         $date = date("Y-m-d");
         if(!preg_match("/".$data->id."/", $log)){
            $status = $this->curl('https://graph.facebook.com/'.$data->id.'/likes?method=post&access_token='.$access_token);
            $x=$data->id."\n";
            $y=fopen('logfeed.txt','a');
            fwrite($y,$x);
            fclose($y);
            if($status == 'true'){
               echo "".$this->COLOR_LIGHT_GREEN."[".$time."]".$this->COLOR_WHITE." ".$data->id." [".$status."]\n";
            }else{
               echo "".$this->COLOR_LIGHT_GREEN."[".$time."]".$this->COLOR_WHITE." ".$data->id." [false]\n";
            }
            sleep($delay);
         }
      }
      $this->Robotlike($limit, $delay, $access_token);
   }
   public function Dashboard($access_token){

      echo "-> 1. ".$this->COLOR_LIGHT_GREEN."Robotlike Timeline ".$this->COLOR_ORANGE."(Automatic like on timeline)".$this->COLOR_WHITE."\n";
      echo "-> 2. ".$this->COLOR_LIGHT_GREEN."Autopoke Friends ".$this->COLOR_ORANGE."(Automatic poke all friends)".$this->COLOR_WHITE."\n";

      echo "Select option : ".$this->COLOR_LIGHT_GREEN."";
      $option = trim(fgets(STDIN));
      echo "".$this->COLOR_WHITE."";
      if($option == '1'){

         echo "\nLimit Feed : ".$this->COLOR_LIGHT_GREEN."";
         $limit = trim(fgets(STDIN));
         echo "".$this->COLOR_WHITE."";
         echo "Delay Second : ".$this->COLOR_LIGHT_GREEN."";
         $delay = trim(fgets(STDIN));
         echo "".$this->COLOR_WHITE."";
         echo "\nVolume Down + C to stop.\n";
         echo "\n-> Robotlike ".$this->COLOR_LIGHT_GREEN."running!\n".$this->COLOR_ORANGE."Please wait collecting feed...\n";
         echo "".$this->COLOR_WHITE."";
         $this->Robotlike($limit, $delay, $access_token);

      }else if($option == '2'){

         echo "\nLimit User : ".$this->COLOR_LIGHT_GREEN."";
         $limit = trim(fgets(STDIN));
         echo "".$this->COLOR_WHITE."";
         echo "Delay Second : ".$this->COLOR_LIGHT_GREEN."";
         $delay = trim(fgets(STDIN));
         echo "".$this->COLOR_WHITE."";
         echo "Gender (male/female) : ".$this->COLOR_LIGHT_GREEN."";
         $genderOption = trim(fgets(STDIN));
         echo "".$this->COLOR_WHITE."";
         echo "\nVolume Down + C to stop.\n";
         echo "\n-> Pokes user ".$this->COLOR_LIGHT_GREEN."running!\n".$this->COLOR_ORANGE."Please wait collecting user online...\n";
         echo "".$this->COLOR_WHITE."";
         $this->PokeUserOnline($limit, $delay, $genderOption, $access_token);

      }else{
         $this->Dashboard($access_token);
      }
   }
   public function MenuLogin(){
      echo "---------------------------------------------\n";
      echo "".$this->COLOR_YELLOW."Facebook".$this->COLOR_WHITE." Robotlike\n";
      echo "Copyright © 2018 ".$this->COLOR_BLUE."Ramadhani Pratama".$this->COLOR_WHITE."\n";
      echo "---------------------------------------------\n";
      echo "".$this->COLOR_LIGHT_GREEN."Userame : ".$this->COLOR_WHITE;
      $username = trim(fgets(STDIN));
      echo "".$this->COLOR_LIGHT_GREEN."Password : ".$this->COLOR_BLACK;
      $password = trim(fgets(STDIN));
      echo "\n";
      echo "".$this->COLOR_ORANGE."Please wait checking username/password ...".$this->COLOR_WHITE;
      echo"\n";
      $this->Login($username, $password);
   }
   public function CheckUser($access_token){
      $api = json_decode($this->curl('https://graph.facebook.com/me?access_token='.$access_token));
      return $api;
   }
   public function Login($username, $password){
      $data = array(
         "api_key" => $this->api_key,
         "email" => $username,
         "format" => "JSON",
         "locale" => "vi_vn",
         "method" => "auth.login",
         "password" => $password,
         "return_ssl_resources" => "0",
         "v" => "1.0"
      );
      $this->SignCreator($data);
      $response = $this->GetToken('GET', false, $data);
      $data = json_decode($response);
      if(!@$data->access_token){
         echo "Failed : ".$this->COLOR_RED."Username/password incorret.".$this->COLOR_WHITE."\n";
         $this->MenuLogin();
      }else{
         $cekUser = $this->CheckUser($data->access_token);
         echo "Success : ".$this->COLOR_LIGHT_GREEN."Success get cookies.".$this->COLOR_WHITE."\n";
         echo "---------------------------------------------\n";
         echo "Account info!\n";
         echo "---------------------------------------------\n";
         echo "".$this->COLOR_WHITE."UserID : ".$this->COLOR_ORANGE."".$cekUser->id."".$this->COLOR_WHITE."\n";
         echo "".$this->COLOR_WHITE."Username : ".$this->COLOR_ORANGE."".$cekUser->username."".$this->COLOR_WHITE."\n";
         echo "".$this->COLOR_WHITE."Name : ".$this->COLOR_ORANGE."".$cekUser->name."".$this->COLOR_WHITE."\n";
         echo "---------------------------------------------\n";
         $this->Dashboard($data->access_token);
      }
   }
   public function SignCreator(&$data){
      $sig = "";
      foreach($data as $key => $value){
         $sig .= "$key=$value";
      }
      $sig .= $this->api_secret;
      $sig = md5($sig);
      return $data['sig'] = $sig;
   }
   public function UserAgent(){
      $user_agents = array(
         "Mozilla/5.0 (iPhone; CPU iPhone OS 9_2_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Mobile/13D15 Safari Line/5.9.5",
         "Mozilla/5.0 (iPhone; CPU iPhone OS 9_0_2 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Mobile/13A452 Safari/601.1.46 Sleipnir/4.2.2m",
         "Mozilla/5.0 (iPhone; CPU iPhone OS 9_3 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13E199 Safari/601.1",
         "Mozilla/5.0 (iPod; CPU iPhone OS 9_2_1 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) CriOS/45.0.2454.89 Mobile/13D15 Safari/600.1.4",
         "Mozilla/5.0 (iPhone; CPU iPhone OS 9_3 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13E198 Safari/601.1"
      );
      $useragent = $user_agents[array_rand($user_agents)];
      return $useragent;
   }
   public function GetToken($method = 'GET', $url = false, $data){
      $c = curl_init();
      $opts = array(
      CURLOPT_URL => ($url ? $url : $this->base).($method == 'GET' ? '?'.http_build_query($data) : ''),
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_USERAGENT => $this->UserAgent());
      if($method == 'POST'){
         $opts[CURLOPT_POST] = true;
         $opts[CURLOPT_POSTFIELDS] = $data;
      }
      curl_setopt_array($c, $opts);
      $d = curl_exec($c);
      curl_close($c);
      return $d;
   }
   public function curl($url, $data=null, $ua=null, $cookie=null){
      $c = curl_init();
      curl_setopt($c, CURLOPT_URL, $url);
      if($data != null){
         curl_setopt($c, CURLOPT_POST, true);
         curl_setopt($c, CURLOPT_POSTFIELDS, $data);
      }
      curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
      if($cookie != null){
         curl_setopt($c, CURLOPT_COOKIE, $cookie);
      }
      if($ua != null){
         curl_setopt($c, CURLOPT_USERAGENT, $ua);
      }
      $hmm = curl_exec($c);
      curl_close($c);
      return $hmm;
   }
}
$open = new TerminalController();
echo $open->MenuLogin();
