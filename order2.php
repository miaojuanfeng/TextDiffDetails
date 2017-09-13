<?php
/*
 * Last modified: 2016-12-01
 * Translate english <-> chinese
 * Agoda modified  + 
 * Axa coupon
 * 
 * */
session_starts();

$is_login = isset($_SESSION['uid']);

$sorder_country = $_GET['country'];
$sorder_departuredate = $_GET['d_date'];
$sorder_arrivaldate = $_GET['a_date'];
$sorder_qty = $_GET['so_qty'];
$sdevice_name = $_GET['device_name'];

$promotion_id =$_GET['promotion_id'];

if($promotion_id != '') $sorder_qty = 1;
if($sorder_country != '' && $sorder_qty =='') {$sorder_qty = 1;}
if($sorder_country == '' && $promotion_id =='') {$sorder_qty = '';}


if(!$is_login) header("Location: login.php?country=$sorder_country&d_date=$sorder_departuredate&a_date=$sorder_arrivaldate&so_qty=1&device_name=$sdevice_name&promotion_id=$promotion_id");
$is_fb_login =$_SESSION['utype'] == 'fb'? true: false;


if($is_login){
  $uname = $_SESSION['uname'];
  $uemail = $_SESSION['uemail'];
  $uid = $_SESSION['uid'];
}

include "inc/top_main_inc.php"; ?>


<?
  if(isset($_GET['promotion_id'])){
    include "admin/modules/model/get_promotion.php";
    $promotion_discount = $promotion['discount']; // 100%
    $is_promotion = true;
    $card_discount = $promotion['card_discount'];
    $promotion_countries = explode(";",$promotion['country']);
    $promotion_date = $promotion['date'];
    
  }
  else{
    $discount = 0;
    $is_promotion = false;
  }
?>
  <script type="text/javascript" src="js/bootstrap-dialog.min.js"></script>
  <link rel="stylesheet" href="css/bootstrap-dialog.min.css"  >

<style>
.ch-label {
  font-size: 15px;
}

</style>

<body style="min-width:1200px;" >
  <? include "inc/header.php"; ?>
  <div id="saveloading"></div>
  <div id="short-order"></div>
  <div id="main_vigual" style="background:url(images/layout/007.jpg) no-repeat center !important;height=590px;">
  <!-- 
    <div class="main_vigual_con">
      <button type="button" class="country_tit">旅遊國家</button>
      <div class="country_select">
        <ul id="country">
        <?php include "admin/modules/model/country.php";
          foreach($countries as $country){
          ?>
          <li><a href="" class="s_country"><input type="hidden" class="country_en" value="<?php echo $country["country"]?>"/><input type="hidden" class="bgimage" value="<?php echo $country["flag"]?>"/><span class="country_ch"><?php echo $country["lang_ch"] == ''? $country["country"]:$country["lang_ch"]  ?></span></a></li>
          <?php 
            }
          ?>
        </ul>
      </div>
    </div>
     -->
  </div>
  <div id="contents_wrap" style="background-color: #fff;padding-left: 20px !important;padding-right: 20px !important;height: auto;overflow:auto;">
    <form id="frmOrder" autocomplete="off">
      <div class="pull-left" style="width: 685px;padding-right: 5px;min-height: 850px;font-size: 15px;">
        <div>
        <?php 
          echo $is_promotion? '<center><h1 style="font-size: 24px;">Promotion</h1></center>': '';
        ?>
        </div>
        <h3 class="h3" style="font-size: 23px;"><?php echo $Language->getText(BOOK_BANANA_WIFI); ?></h3>
        <br/>
        <div>
          <!-- Destination -->
          <div class="pull-left">
          <div class="ch-label"><?php echo $Language->getText(DESTINATION); ?></div>
          <select class="form-control input-sm" name="country" id="country" style="width: 140px;">
            <option value="" disabled selected >Destination</option>
            <?php include "admin/modules/model/country.php";
            
            foreach($countries as $country){
              if($is_promotion == true && $card_discount == 0 && 1==0){
                if(in_array($country['country'],$promotion_countries)){
                  echo '<option ' . $selected . 'value="' . $country['country'] .'">' . ($Language->getLanguage() == 'en'? $country['country'] : $country['lang_ch'] ) . '</option>';
                } 
              }
              else{
            ?>
                <option value="<?php echo $country["country"] ?>" <?php echo ($sorder_country == $country["country"]?"selected" : "") ?>> <?php echo ($Language->getLanguage() == 'en'? $country['country'] : $country['lang_ch'] ) ?> </option>;
            <?php 
              }
            }
            ?>
          </select>
          </div>
          <!-- v_space -->
          <div class="pull-left v-space">&nbsp;</div>
          <div class="pull-left" style="width: 150px;">
          <div class="ch-label"><?php echo $Language->getText(SELECT_PRODUCT1); ?></div>
            <span id="opt_device_name"></span>
            <input type="hidden" name="mno_id">
          </div>
          <!-- v_space -->
          <div class="pull-left v-space">&nbsp;</div>
          <!-- Q'ty -->
          <div class="pull-left">
          <div class="ch-label"><?php echo $Language->getText(QTY); ?></div>
          <select class="form-control input-sm pull-right" name="qty" id="o_qty" style="width: 64px;">
            <option value="" selected >Q'ty</option>
            <?php for($i=1; $i<=10; $i++){ ?>
            <option value="<?php echo $i;?>" <?php echo ($i == $sorder_qty? 'selected' : '');?> ><?php echo $i;?></option>
            <?php }?>
          </select>
          </div>
          <!-- v_space -->
          <div class="pull-left v-space" >&nbsp;</div>
          <!-- departure date -->
          <div class="ch-label"><?php echo $Language->getText(DEPARTURE_DATE); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $Language->getLanguage()=='cn'? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;': '' ;?><?php echo $Language->getText(RETURN_DATE); ?></div>
          <div id="txt_departure_date" class="input-group date pull-left" style="width: 160px;">
              <input type="text" class="form-control input-sm" value="<?php echo $sorder_departuredate ?>" placeholder="Pickup Date" readonly="readonly" style="background-color: white;" id="txtDeparturedate" name="departure_date">
              <div class="input-group-addon">
                  <span class="glyphicon glyphicon-th"></span>
              </div>
          </div>

          <!-- v_space -->
          <div class="pull-left v-space" >&nbsp;</div>
          <!-- arrival date -->
          
          <div id="txt_arrival_date" class="input-group date" style="width: 160px;">
              <input type="text" class="form-control input-sm" value="<?php echo $sorder_arrivaldate ?>" placeholder="Arrival Date" readonly="readonly" style="background-color: white;" id="txtArrivaldate" name="arrival_date">
              <div class="input-group-addon">
                  <span class="glyphicon glyphicon-th"></span>
              </div>
          </div>
          <div id="sSoldout" style="float: right;margin-right: 230px;color: red;display: none;"><?php echo $Language->getText(SOLD_OUT); ?></div>
          <br/>
          <!-- title -->
          <div class="clear-both"></div>
          <div class="pull-left">
            <div class="ch-label"><?php echo $Language->getText(TITLE); ?></div>
            <select class="form-control input-sm" name="title">
              <option value="" disabled selected>Title</option>
              <option value="Mr." >Mr.</option>
              <option value="Ms." >Ms.</option>
              <option value="Mrs." >Mrs.</option>
            </select>
          </div>
          <!-- v_space -->
          <div class="pull-left v-space" >&nbsp;</div>
          <!-- Full name -->
          <div class="pull-left">
            <div class="ch-label"><?php echo $Language->getText(FULLNAME_AS_PASSPORT); ?></div>
            <input type="text" placeholder="Full name" class="form-control input-sm" name="fullname" value="<?php echo ($uid ==0? '': $uname); ?>" />
          </div>
          <!-- v_space -->
          <div class="pull-left v-space" >&nbsp;</div>
          <div class="pull-left">
            <div class="ch-label"><?php echo $Language->getText(EMAIL); ?>  </div>
            <input type="text" placeholder="E-mail" class="form-control input-sm " name="email" value="<?php echo $uemail; ?>" />
          </div>
          <!-- v_space -->
          <div class="pull-left v-space" >&nbsp;</div>
          <div class="pull-left">
            <div class="ch-label"><?php echo $Language->getText(PHONE_NUMBER); ?></div>
            <input type="text" placeholder="Phone Number" class="form-control input-sm" name="phone" style="width: 220px;"/>
          </div>
          

                    
          <!-- voice call -->
          <div class="clear-both"></div>
          <!-- 
          <br>
          <h4 class="h4" style="color:#e08b14;">特別服務</h4>
          <div class="pull-left">
            <input type="checkbox" class="form-control input-sm" style="width: 20px;height: 20px;" name="internet_phone"/>
          </div>
          <div class="pull-left">
            <span>&nbsp;&nbsp;手機租借 HK$20/日</span>&nbsp;&nbsp;<input type="button" value="-" class="btn-primary i_sign i_sign_minus" ><input type="text" value="1" class="i_number" name="i_qty"><input type="button" value="+" class="btn-primary i_sign i_sign_plus">&nbsp;&nbsp;&nbsp;<a target="_blank" href="http://jackkoptrix.wix.com/talkup#!basic/cx7q" onclick="window.open(this.href, 'mywin',
'width=800,height=800'); return false;">(詳情請按此)</a>
            <br>
            <br>
            <div><a href="http://jackkoptrix.wix.com/talkup#!basic/cx7q" onclick="window.open(this.href, 'mywin',
'width=800,height=800'); return false;">包括語音通話 - 無限次從外地致電回港 及 接聽來電</a></div>
          </div>
           -->
        </div>
        
        <!-- Promotion -->
<!--        <br/>
        <div style="color: red;"><?php echo $Language->getText(AVAILABLE_OUT); ?></div>
        <br/> -->
        <br/>
        <br/>
        <h4 class="h4" style="color:#e08b14;"><?php echo $Language->getText(PROMOTIONS); ?></h4>
        <div>
          <div style="border: 1px solid #e8a56f;padding: 10px 20px 10px 20px;width: 637px;">
            <!-- Discount for 4 countries -->
            <div class="pull-left">
              <span><?php echo $Language->getText(EARLYBIRD); ?> &nbsp;</span>
            </div>          
            <div class="pull-left" style="margin-top: -5px;">
              <input type="checkbox" class="form-control input-sm" style="width: 20px;height: 20px;" name="chk_discount"/>
            </div>
            <div class="pull-left" style="padding-left: 20px;">
              <a href="1.jpg" class="pop_info"><?php echo $Language->getText(VIEW_DETAILS); ?></a>
            </div>
            <div class="clear-both"></div>
            <!-- More than 2 devices -->
            <div class="pull-left" style="padding-top: 10px;">
              <span><?php echo $Language->getText(TWIN_OFFER); ?> &nbsp;</span>
            </div>          
            <div class="pull-left" style="padding-top: 5px;">
              <input type="checkbox" class="form-control input-sm" style="width: 20px;height: 20px;" name="chk_more_2_devices"/>
            </div>
            <div class="pull-left" style="padding-left: 20px;padding-top: 10px;">
              <a href="2.jpg" class="pop_info"><?php echo $Language->getText(VIEW_DETAILS); ?></a>
            </div>
            <div class="clear-both"></div>
            <div id="2devices_promotion_msg" style="color: red;display:none;padding-top: 5px;">孖住上優惠不能與任何優惠同時使用！</div>
            <div class="clear-both"></div>
            <!-- EVEN Promotion -->
            
          <div style="display:none;">
                         
            <div class="pull-left" style="padding-top: 10px;">
              <span><?php echo $Language->getText(LIMITED_PROMOTION); ?> &nbsp;</span>
            </div>          
            <div class="pull-left" style="padding-top: 5px;">
              <input type="checkbox" class="form-control input-sm" style="width: 20px;height: 20px;" name="chk_limited_promo"/>
            </div>
            <div class="pull-left" style="padding-left: 20px;padding-top: 10px;">
              <a href="4.jpg" class="pop_info"><?php echo $Language->getText(VIEW_DETAILS); ?></a>
            </div>
            <div class="clear-both"></div>
          </div>
            <!-- Night Promotion -->
            <div id="dvNightpromo">
              <div class="pull-left" style="padding-top: 10px; ">
                <span><?php echo $Language->getText(NIGHT_PROMOTION); ?> &nbsp;</span>
              </div>          
              <div class="pull-left" style="padding-top: 5px; ">
                <input type="checkbox" class="form-control input-sm" style="width: 20px;height: 20px;" name="chk_night_promo"/>
              </div>
              <div class="pull-left" style="padding-left: 20px;padding-top: 10px; ">
                <a href="3.jpg" class="pop_info"><?php echo $Language->getText(VIEW_DETAILS); ?></a>
              </div>
            </div>
            <div class="clear-both"></div>
          </div>
          <br>
          <h4 class="h4" style="color:#e08b14;">Coupons</h4>
          <div>
            <br>
            <div class="pull-left" >
              <?php echo $Language->getText(SELECT_COUPON_TYPE); ?> : &nbsp;&nbsp;
            </div>
            <select name="coupon_type" id="coupon_type" class="form-control input-sm pull-left" style="width: 180px;margin-top: -5px;">
              <option value="banana">Banana WiFi</option>
              <!-- <option value="axa">AXA</option>  -->
            </select>
            <div class="pull-left">&nbsp;&nbsp;&nbsp;</div>
            <div class="pull-left">
              <span><?php echo $Language->getText(BANANA_PROMOTION_CODE)?> : &nbsp;&nbsp;&nbsp;</span>
            </div>
            <div class="pull-left">
            <!-- 
              <select class="form-control input-sm">
              </select> -->
            </div>
            <div class="pull-left">
              <input type="text" class="form-control input-sm" placeholder="Event Coupon Code" id="coupon_number" style="margin: -5px;"  />
            </div>
          </div>
          <div class="pull-left" style="color: red;padding-left: 10px;" id="scoupon_qty"></div>
          <div style="color: red;padding-left: 10px;" id="scoupon_message" class="pull-left"></div>
          <br>
          <div class="clear-both"></div>
          <br/><br/>
          <div style="padding-bottom: 10px !important;" class="pull-left">
            <span><?php echo $Language->getText(AGODA_BOOKING_ID)?> : </span>
          </div>
          <div class="pull-left">&nbsp;&nbsp;</div>
          <div class="pull-left" style="margin-top: -5px;">
            <input type="text" class="form-control input-sm" placeholder="Booking ID" id="agoda_booking_id" name="agoda_booking_id" />
          </div>
          <div class="pull-left">
            
            <div id="agoda_verify" style="display:none;margin-left: 10px;margin-top: 5px;background: url('images/ajax-loader.gif'); width: 16px; height: 16px;"></div>
            <div id="agoda_verify_msg" style="color: red;display:none;padding-left: 10px;padding-top: 5px;" >Invalid Agoda booking id</div>
          </div>
          <div style="clear: both;padding-top:10px;"><?php echo $Language->getText(ONLY_THROUGH) ?> <a href="http://www.agoda.com/bananawifi" target="_new">www.agoda.com/bananawifi</a> <?php echo $Language->getText(YOUR_AGODA_PROMOTION) ?></div>
        </div>
        
        <!-- Pick up & Return -->
        <br><br>
        <h3 class="h3" style="font-size: 23px;"><?php echo $Language->getText(DELIVERY); ?></h3>
        <div  class="clear-both"></div>
        <!-- <div style="color: red;"><?php echo $Language->getText(HOLIDAY); ?></div> -->
        <br>
        <div>
          <div class="clear-both"></div>
          <div class="pull-left">
          <div class="ch-label"><?php echo $Language->getText(PICKUP_DEVICE); ?></div>
            <select class="form-control input-sm" name="pickuplocation" id="pickuplocation">
              <option value="" disabled ><?php echo $Language->getText(PLEASE_SELECT); ?></option>
              <option value="shop"><?php echo $Language->getText(SHOP); ?></option>
              <option value="delivery"><?php echo $Language->getText(COURIER_DELIVERY); ?></option>
              <option value="terminal1" selected ><?php echo $Language->getText(HKIA_T1); ?></option>
              <option value="terminal2"><?php echo $Language->getText(HKIA_T2); ?></option>
            </select>
          </div>
          <!-- Location detail -->
          
          <div class="clear-both"></div>
          <div id="location_detail" >
            
          </div>
          <div class="clear-both"></div>
          <br>
          <div class="pull-left" id="rlocation">
            <div class="ch-label"><?php echo $Language->getText(RETURN_DEVICE); ?></div>
            <select class="form-control input-sm" id="return_location" name="returnlocation">
              <option value="" disabled ><?php echo $Language->getText(PLEASE_SELECT); ?></option>
              <option value="shop"><?php echo $Language->getText(SHOP); ?></option>
              <option value="terminal1" selected ><?php echo $Language->getText(HKIA_T1); ?></option>
              <option value="terminal2"><?php echo $Language->getText(HKIA_T2); ?></option>
            </select>
          </div>
        </div>
        <!-- Location detail -->
        <div id="return_location_detail">
          
        </div>
        <div class="clear-both"></div>

        <!-- Insurance Begins 20170822 -->
        <div class="clear-both"></div>

        <h3 class="h3" style="font-size: 23px;">3. <?php echo $Language->getText(Additional_travel_products); ?></h3>
        <?php include "admin/modules/model/get_edit_care.php";?>
        <div id="insurance" class="pull-left three" style="background-color: #fffddb; width: 100%;">
          <input type="hidden" id="insurance-policyTicket" name="policyTicket" value="">
          <input type="hidden" id="insurance-merchantTradeNo" name="merchantTradeNo" value="">
          <input type="hidden" id="insurance-total" name="insurance_total" value="0" />

          <input type="hidden" name="individual_price" value="0" />
          <input type="hidden" name="family1_price" value="0" />
          <input type="hidden" name="family2_price" value="0" />
          <input type="hidden" name="discount_price" value="0" />

          <h2 class="h2" style="font-size: 19px; color: #e08b14;"><input type="checkbox" name="" id="checkbox_plan" style="width: 20px;height: 20px;margin-top: 0px;" />BananaCare <img src="images/layout/bananacare_logo.png" alt="" style="margin-bottom: 2px;"> <?php echo $Language->getText(Travel_insurance_plan); ?> 
            <span id="insurance-show-sum">
              <span id="realtime-price" style="text-align: right;font-size: 12px; display: inline-block; vertical-align: middle;margin: 0 4px;">
                <?php echo $Language->getText(Preferential_price); ?><br>
                <span class="insurance-plan-name"><?php echo $Language->getText(Insurance_plan_name); ?></span>
              </span>
              <b>HK$<span class="insurance-discount-price"><?=$coupon["special_price"]?></span></b>
              <small id="origin-price" style="color: #222;">(<?php echo $Language->getText(Original_price); ?>：$<span class="insurance-original-price"><?=$coupon["origin_price"]?></span>)</small>
            </span>
            <span id="insurance-show-sum-loading" class="hide">
              <span style="text-align: right;font-size: 12px; display: inline-block; vertical-align: middle;margin: 0 4px;">
                <?php echo $Language->getText(Getting_quotation); ?>...
              </span>
            </span>
          </h2>
          <div class="all-choose">
            <ul class="choose">
              <li class="choose1" id="insurance-section1">
                <!-- Hidden Fields Show here -->
                <input type="hidden" id="insurance-plan" name="plan" value="planC" />
                <input type="hidden" id="insurance-subPlan" name="subPlan" value="individual" />

                <div class="input-group date pull-left" style="width: 160px; margin: 10px 0 20px;">
                    <input type="text" class="form-control input-sm"  placeholder="<?php echo $Language->getText(Departure_date); ?>" readonly="readonly" style="background-color: white;" id="departureDate" name="departureDate">
                    <div class="input-group-addon">
                        <span class="glyphicon glyphicon-th"></span>
                    </div>
                </div>
                <div class="input-group date" style="width: 160px; margin: 10px 0 20px;">
                    <input type="text" class="form-control input-sm" placeholder="<?php echo $Language->getText(Return_date); ?>" readonly="readonly" style="background-color: white;" id="returnDate" name="returnDate">
                    <div class="input-group-addon">
                        <span class="glyphicon glyphicon-th"></span>
                    </div>
                </div>
                <p><span><?php echo $Language->getText(Sub_plan); ?></span><button type="button" class="btn_type active" onclick="insuranceChooseSubPlan('individual')"><?php echo $Language->getText(Individual); ?></button><button type="button" class="btn_type" onclick="insuranceChooseSubPlan('family')"><?php echo $Language->getText(Family); ?></button></p>
                <p class="travel">
                  <span><?php echo $Language->getText(Number_of_passengers); ?></span>
                  <select class="form-control input-sm" name="individual">
                    <option value="1" checked>1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                    <option value="13">13</option>
                    <option value="14">14</option>
                    <option value="15">15</option>
                  </select>
                </p>
                <div class="family clearfix">
                  <div class="col">
                    <span><?php echo $Language->getText(Parent); ?></span>
                    <select class="form-control input-sm" name="parent">
                      <option value="1" checked>1</option>
                      <option value="2">2</option>
                    </select>
                  </div>
                  <div class="col">
                    <span><?php echo $Language->getText(Child); ?></span>
                    <select class="form-control input-sm" name="child">
                      <option value="1" checked>1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                      <option value="4">4</option>
                      <option value="5">5</option>
                      <option value="6">6</option>
                      <option value="7">7</option>
                      <option value="8">8</option>
                      <option value="9">9</option>
                      <option value="10">10</option>
                      <option value="11">11</option>
                      <option value="12">12</option>
                      <option value="13">13</option>
                      <option value="14">14</option>
                    </select>
                  </div>
                  <div class="col">
                    <span><?php echo $Language->getText(Other_passengers); ?></span>
                    <select class="form-control input-sm" name="other">
                      <option value="0" checked>0</option>
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                      <option value="4">4</option>
                      <option value="5">5</option>
                      <option value="6">6</option>
                      <option value="7">7</option>
                      <option value="8">8</option>
                      <option value="9">9</option>
                      <option value="10">10</option>
                      <option value="11">11</option>
                      <option value="12">12</option>
                      <option value="13">13</option>
                    </select> 
                  </div>
                </div>
              </li>


              <!-- <li class="choose2">
                <p><span style="color: #e08b14; margin-right: 10px;">同行人數</span><span style="color: #e08b14;">2旅客</span></p>
                <div class="travel_number No1"> 
                  <div style="margin-bottom: 10px;">旅客1<small>（必須為申請人）</small></div>
                  <input type="text" value="cvbnb" class="form-control input-sm" name="">
                  <input type="text" value="z123456" class="form-control input-sm" name="">
                  <input type="text" value="01-01-1992" class="form-control input-sm" name="">
                  <select class="form-control input-sm">
                    <option value="1" checked>18-70</option>
                    <option value="2">18</option>
                    <option value="3">19</option>
                    <option value="4">20</option>
                  </select>
                </div>
                <div class="travel_number No2"> 
                  <div style="margin-bottom: 10px;">旅客2</div>
                  <input type="text" placeholder="Name" class="form-control input-sm" name="">
                  <select class="form-control input-sm">
                    <option value="1" checked>HKID</option>
                    <option value="2">dfgffsdfbc</option>
                    <option value="3">dfgffsdfbc</option>
                  </select>
                  <input type="text" value="01-01-1992" class="form-control input-sm" name="">
                  <select class="form-control input-sm">
                    <option value="1" checked>Age Range</option>
                    <option value="2">12</option>
                    <option value="3">13</option>
                    <option value="4">14</option>
                  </select>
                </div>
                <p>
                  <input type="checkbox" name="" style="width: 20px;height: 20px;margin-top: 0px;" />本人已參閱及明白富衛<a href="#">收集個人資料聲明</a>，并同意接受其約束。
                </p>
                <p>
                  <input type="checkbox" name="" style="width: 20px;height: 20px;margin-top: 0px;" />本人已參閱及明白保障範圍并聲明。
                </p>
              </li> -->
              <li id="insurance-section2" class="choose2">

                <div id="insurance-plan-family" class="hide">
                  <p>
                    <span style="color: #e08b14; margin-right: 10px;"><?php echo $Language->getText(Number_of_Insured_Person); ?></span>
                    <span style="color: #e08b14; margin-right: 10px;"><span class="insurance-plan-family-parent-no">1</span> <?php echo $Language->getText(Parent); ?></span>
                    <span style="color: #e08b14; margin-right: 10px;"><span class="insurance-plan-family-child-no">2</span> <?php echo $Language->getText(Child); ?></span>
                    <span style="color: #e08b14;"><span class="insurance-plan-family-other-no">3</span> <?php echo $Language->getText(Other_passengers); ?></span>
                  </p>

                  <div class="family_number"> 
                    <div><?php echo $Language->getText(Parent); ?>1<small>（必須為申請人）</small></div>
                    <input type="text" value="" class="form-control input-sm pull-left first-person-name" name="applicant[name]" placeholder="<?php echo $Language->getText(Name2); ?>">
                    <input type="text" value="" class="form-control input-sm pull-left" name="applicant[hkid]" placeholder="<?php echo $Language->getText(HKID2); ?>" style="max-width: 160px;">
                    <div class="input-group date pull-left" style="width: 138px; margin: 10px 0;">
                        <input type="text" class="form-control input-sm applicant_dob"  placeholder="<?php echo $Language->getText(Brithday); ?>" readonly="readonly" style="background-color: white; margin-top:0;" name="applicant[dob]">
                        <div class="input-group-addon" style="float:left;padding-right:25px;">
                            <span class="glyphicon glyphicon-th"></span>
                        </div>
                    </div>
                    <!-- <input type="text" value="" class="form-control input-sm" name="applicant[dob]" placeholder="<?php echo $Language->getText(Brithday); ?>" style="max-width: 140px;"> -->
                    <!-- <input type="text" value="" class="form-control input-sm first-person-phone" name="applicant[contactNo]" placeholder="<?php echo $Language->getText(Mobile_number); ?>" style="max-width: 160px;"> -->
                    <select class="form-control input-sm pull-left applicant_ageRangeCode" name="applicant[ageRangeCode]" style="max-width:120px;">
                      <option value="" checked><?php echo $Language->getText(Age_range); ?></option>
                      <option value="1"><?php echo $Language->getText(Age1); ?></option>
                      <option value="2"><?php echo $Language->getText(Age2); ?></option>
                      <option value="3"><?php echo $Language->getText(Age3); ?></option>
                    </select>
                    <!-- <input type="email" value="" class="form-control input-sm first-person-email" name="applicant[email]" placeholder="<?php echo $Language->getText(Email_address); ?>" style="max-width: none; width: 80%;"> -->
                    <div class="clearfix"></div>
                  </div>

                  <div id="insurance-plan-family-parent">
                    
                  </div>

                  <div id="insurance-plan-family-child">
                    
                  </div>

                  <div id="insurance-plan-family-other">

                  </div>
                  
                  <br>
                  <p>
                    <input type="checkbox" name="directOptOut" style="width: 20px;height: 20px;margin-top: 0px;" />本人已參閱及明白富衛<a href="#">收集個人資料聲明</a>，并同意接受其約束。
                  </p>
                  <p>
                    <input type="checkbox" name="thirdPartyOptOut" style="width: 20px;height: 20px;margin-top: 0px;" />本人已參閱及明白保障範圍并聲明。
                  </p>

                </div>


                <div id="insurance-plan-individual" class="hide">
                  <p>
                    <span style="color: #e08b14; margin-right: 10px;"><?php echo $Language->getText(Number_of_Insured_Person); ?></span>
                    <span style="color: #e08b14;"><span class="insurance-plan-individual-no">3</span><?php echo $Language->getText(Applicant); ?></span>
                  </p>

                  <div class="family_number"> 
                    <div><?php echo $Language->getText(Applicant); ?>1<small>（必須為申請人）</small></div>
                    <input type="text" value="" class="form-control input-sm pull-left first-person-name" name="applicant[name]" placeholder="<?php echo $Language->getText(Name2); ?>">
                    <input type="text" value="" class="form-control input-sm pull-left" name="applicant[hkid]" placeholder="<?php echo $Language->getText(HKID2); ?>" style="max-width: 160px;">
                    <div class="input-group date pull-left" style="width: 138px; margin: 10px 0;">
                        <input type="text" class="form-control input-sm applicant_dob"  placeholder="<?php echo $Language->getText(Brithday); ?>" readonly="readonly" style="background-color: white; margin-top:0;" name="applicant[dob]">
                        <div class="input-group-addon" style="float:left;padding-right:25px;">
                            <span class="glyphicon glyphicon-th"></span>
                        </div>
                    </div>
                    <!-- <input type="text" value="" class="form-control input-sm" name="applicant[dob]" placeholder="<?php echo $Language->getText(Brithday); ?>" style="max-width: 140px;"> -->
                    <!-- <input type="text" value="" class="form-control input-sm first-person-phone" name="applicant[contactNo]" placeholder="<?php echo $Language->getText(Mobile_number); ?>" style="max-width: 160px;"> -->
                    <select class="form-control input-sm pull-left applicant_ageRangeCode" name="applicant[ageRangeCode]" style="max-width:120px;">
                      <option value="" checked><?php echo $Language->getText(Age_range); ?></option>
                      <option value="1"><?php echo $Language->getText(Age1); ?></option>
                      <option value="2"><?php echo $Language->getText(Age2); ?></option>
                      <option value="3"><?php echo $Language->getText(Age3); ?></option>
                    </select>
                    <!-- <input type="email" value="" class="form-control input-sm first-person-email" name="applicant[email]" placeholder="<?php echo $Language->getText(Email_address); ?>" style="max-width: none; width: 80%"> -->
                    <div class="clearfix"></div>
                  </div>
                  
                  <div id="insurance-plan-individual-individual">
                    
                  </div>
                  <br>
                  <p>
                    <input type="checkbox" name="directOptOut" style="width: 20px;height: 20px;margin-top: 0px;" />本人已參閱及明白富衛<a href="#">收集個人資料聲明</a>，并同意接受其約束。
                  </p>
                  <p>
                    <input type="checkbox" name="thirdPartyOptOut" style="width: 20px;height: 20px;margin-top: 0px;" />本人已參閱及明白保障範圍并聲明。
                  </p>

                </div>
                
              </li>
              <li class="choose3">
                <h5>多謝閣下選購，以下是您的保單摘要：</h5>
                <div class="guest_info">
                  <p><span><?php echo $Language->getText(Insured_period); ?></span><span>：<?php echo $Language->getText(From); ?> <span id="insurance-departureDate"></span> <?php echo $Language->getText(To); ?> <span id="insurance-returnDate"></span></span></p>
                  <p><span><?php echo $Language->getText(No_of_date); ?></span><span>：<span id="insurance-totalDay">0</span></span></p>
                  <p><span><?php echo $Language->getText(No_of_insured_applicant); ?></span><span>：<span id="insurance-totalInsuredPerson">0</span></span></p>
                  <p><span><?php echo $Language->getText(Price); ?></span><span>：HK$<span id="insurance-totalDue">0</span></span></p>
                </div>
                
                <table>
                  <thead>
                    <tr>
                      <td><?php echo $Language->getText(Name); ?></td>
                      <td><?php echo $Language->getText(English_name); ?></td>
                      <td><?php echo $Language->getText(Age_range); ?></td>
                      <td><?php echo $Language->getText(HKID); ?></td>
                    </tr>
                  </thead>
                  <tbody id="insurance-insuredPersonTable">
                    <tr>
                      <td>旅客1</td>
                      <td>Chan Tai Man</td>
                      <td>28</td>
                      <td>Z123423454</td>
                    </tr>
                    <tr>
                      <td>旅客2</td>
                      <td>Chan Tai Man</td>
                      <td>28</td>
                      <td>Z123423454</td>
                    </tr>
                  </tbody>
                </table>
                <p>付款成功後，該保單憑證將會發送到閣下登記WiFi租借之電郵內。</p>
              </li>
            </ul>
            <div class="cover"></div>
            <div class="btn-left"><u></u></div>
            <div class="btn-right hide"><u></u></div>
          </div>
          <!-- 2nd Slide -->
          <!-- Michael Start -->
          <?php
          if( $Language->getLanguage() == 'cn' ){
            echo str_replace('\\', '', $coupon['description_tc']);
          }else if( $Language->getLanguage() == 'en' ){
            echo str_replace('\\', '', $coupon['description_en']);
          }
          ?>
          <!-- <p style="padding-left: 40px;"><span style="margin-right: 20px;">HK$XXX 醫療保險</span><span>HK$XXX WiFi裝置保障</span></p>
          <div class="range">
            <p style="padding-left: 40px;"><span class="toggle glyphicon glyphicon-plus"></span><span>承保範圍：</span></p>
            <div class="toggle-content">
              <ul class="type">
                <li class="clearfix"><em></em><span class="float-left">住院或檢疫現金津貼</span><span class="float-right">HK$XXX</span></li>
                <li class="clearfix"><em></em><span class="float-left">全球緊急支援服務</span><span class="float-right">HK$XXX</span></li>
                <li class="clearfix"><em></em><span class="float-left">旅程延誤</span><span class="float-right">HK$XXX</span></li>
                <li class="clearfix"><em></em><span class="float-left">行李損失</span><span class="float-right">HK$XXX</span></li>
                <li class="clearfix"><em></em><span class="float-left">旅遊證件遺失</span><span class="float-right">HK$XXX</span></li>
              </ul>
              <ul class="select-group">
                <li>
                  <div class="bg-orange">產品特點<u></u></div>
                  <ul class="type">
                    <li class="clearfix"><em></em><span class="float-left">住院或檢疫現金津貼</span><span class="float-right">HK$XXX</span></li>
                    <li class="clearfix"><em></em><span class="float-left">全球緊急支援服務</span><span class="float-right">HK$XXX</span></li>
                    <li class="clearfix"><em></em><span class="float-left">旅程延誤</span><span class="float-right">HK$XXX</span></li>
                    <li class="clearfix"><em></em><span class="float-left">行李損失</span><span class="float-right">HK$XXX</span></li>
                    <li class="clearfix"><em></em><span class="float-left">旅遊證件遺失</span><span class="float-right">HK$XXX</span></li>
                  </ul>
                </li>
                <li>
                  <div class="bg-orange">保障範圍<u></u></div>
                  <ul class="type">
                    <li class="clearfix"><em></em><span class="float-left">住院或檢疫現金津貼</span><span class="float-right">HK$XXX</span></li>
                    <li class="clearfix"><em></em><span class="float-left">全球緊急支援服務</span><span class="float-right">HK$XXX</span></li>
                    <li class="clearfix"><em></em><span class="float-left">旅程延誤</span><span class="float-right">HK$XXX</span></li>
                    <li class="clearfix"><em></em><span class="float-left">行李損失</span><span class="float-right">HK$XXX</span></li>
                    <li class="clearfix"><em></em><span class="float-left">旅遊證件遺失</span><span class="float-right">HK$XXX</span></li>
                  </ul>
                </li>
                <li>
                  <div class="bg-orange">主要不受保項目<u></u></div>
                  <ul class="type">
                    <li class="clearfix"><em></em><span class="float-left">住院或檢疫現金津貼</span><span class="float-right">HK$XXX</span></li>
                    <li class="clearfix"><em></em><span class="float-left">全球緊急支援服務</span><span class="float-right">HK$XXX</span></li>
                    <li class="clearfix"><em></em><span class="float-left">旅程延誤</span><span class="float-right">HK$XXX</span></li>
                    <li class="clearfix"><em></em><span class="float-left">行李損失</span><span class="float-right">HK$XXX</span></li>
                    <li class="clearfix"><em></em><span class="float-left">旅遊證件遺失</span><span class="float-right">HK$XXX</span></li>
                  </ul>
                </li>
                <li>
                  <div class="bg-orange">年齡限制<u></u></div>
                  <ul class="type">
                    <li class="clearfix"><em></em><span class="float-left">住院或檢疫現金津貼</span><span class="float-right">HK$XXX</span></li>
                    <li class="clearfix"><em></em><span class="float-left">全球緊急支援服務</span><span class="float-right">HK$XXX</span></li>
                    <li class="clearfix"><em></em><span class="float-left">旅程延誤</span><span class="float-right">HK$XXX</span></li>
                    <li class="clearfix"><em></em><span class="float-left">行李損失</span><span class="float-right">HK$XXX</span></li>
                    <li class="clearfix"><em></em><span class="float-left">旅遊證件遺失</span><span class="float-right">HK$XXX</span></li>
                  </ul>
                </li>
              </ul>
              <p style="margin: 20px 0; width: 68%;">*「BananaCare旅遊保險計劃」由FWD富衛旅遊保險承保；Banana WiFi Limited為FWD富衛保險有限公司委任之保險代理。</p>
            </div>
            
          </div> -->
          <!-- Michael End -->
        </div>
        <style>
        .clearfix:before, .clearfix:after {
          content: "";
          display: table;
        }
        .clearfix:after {
          clear: both;
        }
        #checkbox_plan{
          cursor: pointer;
        }
        .three p{
          margin: 10px 0;
        }
        .three span{
          display: inline-block;
        }
        .range .toggle{
          width: 17px;
          height: 17px;
          border: 1px solid #aaa;
          text-align: center;
          line-height: 14px;
          display: inline-block;
          vertical-align: middle;
          margin-right: 12px;
          cursor: pointer;
          font-size: 12px;
          color: #555;
          transition: all 1s;
          -moz-transition: all 1s;  /* Firefox 4 */
          -webkit-transition: all 1s; /* Safari 和 Chrome */
          -o-transition: all 1s;  /* Opera */
        }
        .range p span{
          vertical-align: middle;
        }
        .toggle-content{
          padding-left: 40px;
          display: none;
        }
        .float-left{
          float: left;
        }
        .float-right{
          float: right;
        }
        .range .type{
          padding-left: 34px;
          width: 50%;
        }
        .type em{
          width: 12px;
          height: 12px;
          background-color: #555;
          border-radius: 50%;
          margin-right: 5px;
          float: left;
        }
        .select-group{
          margin-top: 10px;
        }
        .select-group li{
          margin-bottom: 1px;
        }
        .select-group .bg-orange{
          width: 60%;
          background-color: #e08b14;
          padding: 5px 10px;
          position: relative;
          cursor: pointer;
        }
        .select-group u{
          width: 0;
          height: 0;
          display: inline-block;
          border-top: 6px solid #555; 
          border-right: 6px solid transparent;
          border-bottom: 6px solid transparent;
          border-left: 6px solid transparent;
          position: absolute;
          top: 50%;
          right: 10px;
          margin-top: -3px;
        }
        .select-group .type{
          margin: 10px 0;
          display: none;
        }
        .all-choose{
          position: relative;
          width: 95%;
          padding-left: 40px;
          margin-bottom: 20px;
            overflow: hidden;
            display: none;
        }
        .choose{
          padding: 0;
          width: 300%;
          overflow: hidden;
        }
        .choose li{
          width: 554px;
            padding-bottom: 10px;
            float: left;
            min-height: 175px;
            border-bottom: 1px solid #555;
        }
        .choose1 .btn_type{
          padding: 6px 28px;
          background-color: #eee;
            border-radius: 0.5em;
            margin: 0 5px;
            border: 2px solid #aaa;
            cursor: pointer;
        }
        .choose1 .active{
          background-color: #e08b14;
          color: #fff;
        }
        .choose1 .input-group{
          margin-right: 4px;
        }
        .choose1 p{
          margin: 20px 0;
        }
        .choose1 span{
          margin-right: 10px;
        }
        .input-group-addon span{    
          margin: 0;    
        }
        .choose1 select{
          min-width: 40px;
          width: auto;
            display: inline-block;
        }
        .choose1 .col{
          float: left;
          width: 30%;
        }
        .choose1 .family{
          display: none;
        }
        .choose2 .input-sm{
          width: auto;
          display: inline-block;
          max-width: 100px;
          margin-top: 10px;   
        }   
        #insurance-plan-individual-individual select,   
        #insurance-plan-family-child select,    
        #insurance-plan-family-other select{    
            /*margin: 10px 4px 0;*/
        }
        .travel_number,.family_number{
          margin: 10px 0;
        }
        /*.choose3{
          display: none;
        }*/
        .choose3 h5{
          margin: 10px 0;
          color: #e08b14;
          font-size: 18px;
        }
        .choose3 .guest_info{
          margin: 10px 0;
        }
        .choose3 .guest_info p{
          margin: 2px 0;
        }
        .choose3 .guest_info p>span:first-child{
          min-width: 80px;
        }
        .choose3 table{
          width: 90%;
          border: 2px solid #e08b14;
        }
        .choose3 table thead{
          background-color: #e08b14;
          color: #fff;
        }
        .choose3 table tr{
          border-bottom: 2px solid #e08b14;
        }
        .choose3 table tr:last-child{
          border: none;
        }
        .choose3 table td{
          text-align: center;
          padding: 5px;
        }
        .btn-left{
          width: 25px;
          height: 50px;
          background-color: #e08b14;
          border-radius: 50px 0 0 50px;
          position: absolute;
          left: 0;
          top: 50%;
          margin-top: -25px;
          cursor: pointer;
          display: none;
        }
        .btn-left u{
          width: 0;
          height: 0;
          display: inline-block;
          border-top: 9px solid transparent; 
          border-right: 9px solid  #fff;
          border-bottom: 9px solid transparent;
          border-left: 9px solid transparent;
          position: absolute;
          top: 50%;
          left: 0px;
          margin-top: -9px;
        }
        .btn-right{
          width: 25px;
          height: 50px;
          background-color: #e08b14;
          border-radius: 0 50px 50px 0;
          position: absolute;
          right: 0;
          top: 50%;
          margin-top: -25px;
          cursor: pointer;
        }
        .btn-right u{
          width: 0;
          height: 0;
          display: inline-block;
          border-top: 9px solid transparent; 
          border-right: 9px solid transparent;
          border-bottom: 9px solid transparent;
          border-left: 9px solid #fff;
          position: absolute;
          top: 50%;
          right: 0px;
          margin-top: -9px;
        }
        .cover{
          width: 40px;
          height: 100%;
          position: absolute;
          left: 0;
          top: 0;
          background-color: #fffddb;
        }
        </style>
        <!-- <script type="text/javascript" src="js/fwd.js"></script> -->
        <div class="clear-both"></div>
        <!-- Insurance Ends 20170822 -->

        <!-- Payment -->
        <br>
        <div  class="clear-both"></div>
        <h3 class="h3" style="font-size: 23px;"><?php echo $Language->getText(PAYMENT_OPTIONS); ?></h3>
        <div class="pull-left">
          <div class="ch-label"><?php echo $Language->getText(PAYMENT_METHOD); ?></div>
          <select class="form-control input-sm" id="payment_type" name="payment_type" >
            <option value="" disabled ><?php echo $Language->getText(PLEASE_SELECT); ?></option>
            
            <option value="card" selected ><?php echo $Language->getText(CREDIT_CARD_VISA); ?></option>
            
            <option value="bank"><?php echo $Language->getText(BANK_TRANSFER); ?></option>
          </select>
          <br>
          <div>
            <div id="pay_card_info" style="padding-bottom: 10px;"><?php echo $Language->getText(CREDIT_CARD_DEPOSIT); ?> <br>
            <?php echo $Language->getText(BANK_TRANSFER_CONDITION); ?></div>
            <div id="pay_bank_info" style="display:none;" ><?php echo $Language->getText(DEPOSIT_HKD); ?></div>
          </div>
          <br>
          <div id="card_payment" style="display:none;padding-top: 10px;">
            <div>
              <input type="radio" name="card_type" value="visa"> <img src="images/cards_visa.gif" width="32px" /> Visa Card
              <input type="radio" name="card_type" value="master"> <img src="images/master card.png" width="32px"/> Master Card
              &nbsp;
              <span id="card_type_required" style="color: red;font-style: italic;"></span>
            </div>
            <br>
            <div>
              <div>Card Number</div>
              <input type="text" class="form-control input-sm" placeholder="Required" name="card_number" >
            </div>
            <br>
            <div class="pull-left">
              <div>Expire Date</div>
              <div class="pull-left">
                <input type="text" class="form-control input-sm" placeholder="mm" style="width: 70px;" class="pull-left" name="card_expire_month">
              </div>
              <div class="pull-left">
                <input type="text" class="form-control input-sm" placeholder="yyyy" style="width: 70px;" class="pull-left" name="card_expire_year">
              </div>
            </div>
            <div class="pull-left">
              <div>Security Code</div>
              <input type="text" class="form-control input-sm" placeholder="required" name="card_security_code">
            </div>
            <br><br>
             <input type="button" id="testpay" value="Test payment" /> 
          </div>
          <div id="bank_payment" style="display:none;">
            <!-- 
            <div>Banana WiFi 的銀行帳戶</div>
            <select class="form-control input-sm" id="bank_account" name="bank_account">
              <option value="" disabled selected>請選擇</option>
              <option value="HSBC Acc.No. 634-434500-001">匯豐銀行（HSBC）帳戶號碼 634-434500-001  帳戶名稱  Banana WiFi Limited</option>
              <option value="BOC Acc. No. 012-802-0-0113051">中國銀行（BOC）帳戶號碼 012-802-0-0113051  帳戶名稱  Banana WiFi Limited</option>
              <option value="HASE Acc. No. 789-284221-883">恆生銀行（HASE）帳戶號碼 789-284221－883  帳戶名稱  Banana WiFi Limited</option>
              <option value="SCB Acc. No. 574-1-165492-3">渣打銀行 （SCB ）帳戶號碼 574-1-165492-3    帳戶名稱  Banana WiFi Limited</option>
            </select>
             -->
            <div><?php echo $Language->getText(REFUND_ACCOUNT); ?></div>
            <div><?php echo $Language->getText(YOUR_BANK_ACCOUNT); ?></div>
            <select class="form-control input-sm pull-left" id="return_bank_account" name="refund_bank" style="width: 150px;">
              <option value="" disabled selected><?php echo $Language->getText(PLEASE_SELECT); ?></option>
              <option value="匯豐銀行 HSBC"><?php echo $Language->getText(HSBC_BANK); ?></option>
              <option value="恒生銀行 HASE"><?php echo $Language->getText(HASE_BANK); ?></option>
              <option value="中國銀行 BOC"><?php echo $Language->getText(BOC_BANK); ?></option>
              <option value="渣打銀行 SCB"><?php echo $Language->getText(SCB_BANK); ?></option>
              <option value="東亞銀行 BEA"><?php echo $Language->getText(BEA_BANK); ?></option>
              <option value="星展銀行 DBS"><?php echo $Language->getText(DBS_BANK); ?></option>
              <option value="花旗銀行 CITI"><?php echo $Language->getText(CITI_BANK); ?></option>
              <option value="建設銀行 CCB"><?php echo $Language->getText(CCB_BANK); ?></option>
              <option value="大新銀行 DSB"><?php echo $Language->getText(DSB_BANK); ?></option>
              <option value=""><?php echo $Language->getText(OTHER); ?></option>
              
            </select>
            <input type="text" placeholder="<?php echo $Language->getText(PLEASE_TYPE); ?>" class="form-control input-sm pull-left" name="refund_bank_account" style="width: 275px;"/>
          </div>
        </div>
      </div>
      
    
      <!-- right side -->
      <div class="pull-right" style="font-size: 15px;">
        <br>
        <div class="border-radius-5" style="background-color: #fcc949;padding: 10px 10px 30px 10px;">
          <!-- Signin 
          <div>
            <input type="button" value="SIGN IN / CREATE ACCOUNT" />
            <input type="button" value="CONTINUE AS GUEST" />
            <fb:login-button scope="public_profile,email" onlogin="checkLoginState();">
              <span>Login with Facebook</span>
            </fb:login-button>
          </div>-->
          <!-- Order Summary -->
          <div style="font-size: 16px;">
            <center><div><h2 class="h3" style="margin-bottom: 15px !important;margin-top: 10px !important;font-size: 18px;"><b><?php echo $Language->getText(ORDER_SUMMARY) ?></b></h2></div></center>
            
            <table id="tbldetail" class="table table-bordered" style="background-color: #fff;font-size: 15px;" >
              <tr>
                <th class="highlight" style="font-weight: normal !important;"><?php echo $Language->getText(DEPARTURE_DATE); ?></th>
                <td width="100px"><b><span id="d_date"></span></b></td>
                <th class="highlight" style="font-weight: normal !important;"><?php echo $Language->getText(RETURN_DATE); ?></th>
                <td ><b><span id="a_date"></span></b></td>
              </tr>
              <tr>
                <th class="td-label highlight" style="font-weight: normal !important;"><?php echo $Language->getText(TRAVEL_DAYS); ?></th>
                <td ><div id="total_days"></div></td>
                <th class="td-label highlight" style="font-weight: normal !important;" ><?php echo $Language->getText(DESTINATION_COUNTRY); ?></th>
                <td width="100px"><span id="destination"></span></td>
              </tr>
            </table>
            
            <table class="table table-bordered" style="background-color: #fff;font-size: 15px;">
              <tr>
                <th class="hightlight"  colspan="2"><center><?php echo $Language->getText(DESCRIPTION); ?></center></th>
                <th><center><?php echo $Language->getText(QUANTITY); ?></center></th>
                <th><center><?php echo $Language->getText(SUB_TOTAL); ?></center></th>
              </tr>
              <tr>
                <th width="120px"  class="hightlight" style="font-weight: normal !important;">Pocket Wi-Fi</th>
                <td width="120px"><span id="product_desc" ><?php echo $_REQUEST['device_name']?></span><br><span id="product_dailyplan" ></span></td>
                <td><span id="qty"></span></td>
                <td style="text-align: right;"><span id="cost"></span></td>
              </tr>
              <tr style="display: none;">
                <th class="hightlight" style="font-weight: normal !important;"><?php echo $Language->getText(INTERNETPHONE); ?></th>
                <td></td>
                <td><span id="phone_qty"></span></td>
                <td style="text-align: right;"><span id="phone_cost"></span></td>
              </tr>
              <tr>
                <th class="hightlight" style="font-weight: normal !important;"><?php echo $Language->getText(DEPOSIT); ?></th>
                <td></td>
                <td></td>
                <td style="text-align: right;"><span id="deposit">0</span></td>
              </tr>
              <tr>
                <th class="hightlight" style="font-weight: normal !important;"><?php echo $Language->getText(PROMOTION); ?></th>
                <td style="width: 100px; overflow-x: hidden; "><div id="s5agoda_discount"></div><div id="s4countries_discount"></div><div id="scoupon_discount"></div><div id="s2devices_discount"></div><div id="snight_discount"></div><div id="slimited_discount"></div></td>
                <td></td>
                <td style="text-align: right;"><div id="discount5" ></div><div id="discount_4" ></div><div id="discount" ></div><div id="discount2" ></div><div id="discount3" ></div><div id="discount6" ></div></td>
              </tr>
              <tr>
                <th class="hightlight" style="font-weight: normal !important;"><?php echo $Language->getText(DELIVERY_FEE); ?></th>
                <td></td>
                <td></td>
                <td style="text-align: right;"><span id="delivery_cost">0</span></td>
              </tr>
              <tr>
                <th class="hightlight" style="font-weight: normal !important;">Banana Care</th>
                <td id="banana_care_name"></td>
                <td></td>
                <td style="text-align: right;"><span id="banana_care">0</span></td>
              </tr>
              <tr>
                <th colspan="3" style="background-color: #fff;font-weight: normal !important;"><?php echo $Language->getText(TOTAL_COST); ?></th>
                
                <th style="background-color: #fff;text-align: right;"><span id="total_cost">0</span></th>
              </tr>
            </table>
            
            
            <div><h4 class="h4">《<?php echo $Language->getText(POLICY_USAGE); ?>》 </h4></div>
            <div style="text-align: justify;width: 450px;font-size: 15px;">
              <p style="line-height: 150%;">
              <?php echo $Language->getText(POLICY1); ?>
              </p>
            </div>
            <br/><br/>
            <div>
              <div class="pull-left" id="agreewrap" >
                <input type="checkbox" name="agree" style="width: 20px;height: 20px;margin-top: 0px;" />
              </div>
              <div style="padding-left: 10px;padding-top: 0px;">
                &nbsp;<?php echo $Language->getText(BY_CLICKING_THIS); ?><a href="http://bananawifi.com/sub/sub01_01.php?section=terms" target="_blank"><?php echo $Language->getText(TERMS_AND_CONDITIONS); ?></a>
                </div>
              <br><br>
            </div>
          </div>
          <center><input type="button" value="<?php echo $Language->getText(SUBMIT_ORDER); ?>" class="form-control input-lg btn-primary" style="width:80%" id="btnSumitOrder" /></center>
        </div>
      </div>
      </form>
      
      <form name="payForm" id="payForm" action="migspay.php" method="post">
        <input type="hidden" name="amount" value="3.0" >
        <input type="hidden" name="orderRef" value="000000000000">
      </form>
  </div>
  <? include "inc/footer.php"; ?>
  
  <script type="text/javascript" async="async" defer="defer" data-cfasync="false" src="https://mylivechat.com/chatinline.aspx?hccid=59442795"></script>
</body>
<script>
// TODO: michael 如何拿取submit之後的價錢
// TODO: 身份證標準化，日期標準化

var ageGroup=["<?php echo $Language->getText(Age1); ?>", "<?php echo $Language->getText(Age2); ?>", "<?php echo $Language->getText(Age3); ?>"];

function insuranceChooseSubPlan(plan){
  $('#insurance #insurance-subPlan').val(plan);
  if(plan == 'individual'){
    $("#insurance .travel").show();
    $("#insurance .family").hide();
  } else {
    $('#insurance-section1 select[name="child"]').trigger("change");
    $("#insurance .family").show();
    $("#insurance .travel").hide();
  }
  
  $('#insurance #insurance-subPlan').trigger("change");
}

$(function(){
  var today = '<?php echo date('Y-m-d')?>';
  var promotion_discount = <?php echo $promotion_discount == ''?0:$promotion_discount; ?>;
  getDeviceName('<?php echo $_REQUEST['country']?>');

  var coupon = null;
  var daily_plan = '';
  var agoda_booking = null;
  
  /* Init order summary value */
  ordersummary();

  /* trigger default select pickup & return*/
  
  
  $('#pickuplocation').change(function(){


    // Holiday shop closed
    if($(this).val() == 'shop' || $(this).val() == 'delivery'){
      if($('#txtDeparturedate').val() == '' || $('#txtArrivaldate').val() == ''){
        
        $('#pickuplocation option[value=""]').prop('selected', true);
      }
      if($('#txtDeparturedate').val() != '' || $('#txtArrivaldate').val() != ''){
        day = daydiff(parseDate($('[name="departure_date"]').val()), parseDate($('[name="arrival_date"]').val()));
        if(day < 3){
          if($(this).val() == 'shop'){
            alert("<?php echo $Language->getText(SHOP_3DAYS) ?>");
          }
          if($(this).val() == 'delivery'){
            alert("<?php echo $Language->getText(DELIVERY_3DAYS) ?>");
          }
          $('#pickuplocation').val('terminal1');
        }
      }
      
    }

    // end holiday
    
    var data = 'lang=<?php echo $language ?>&t=pickup&p_type=' + $(this).val() + '&departure_date=' + $('[name="departure_date"]').val();
    if($(this).val() == 'delivery'){
      $('#delivery_cost').html('80');
      ordersummary();
      $('#rlocation').attr('style','display:none');
      $('#return_location_detail').attr('style','display:none');
      
    }
    else{

      $('#delivery_cost').html('0');
      $('#rlocation').removeAttr('style');
      $('#return_location_detail').removeAttr('style');
      ordersummary();
    }

    if($(this).val() == 'terminal1' || $(this).val() == 'terminal2'){
      //$('#return_location option[value="shop"]').hide();
      $('#return_location option[value="shop"]').remove();
    }
    else {
      //$('#return_location option[value="shop"]').show();
      //$('#return_location').append();
      
      shop_return_close = isShopClosed($('[name="arrival_date"]').val());
      
      if($('#return_location option[value="shop"]').length == 0 && shop_return_close == false){
        $("#return_location option").eq(1).before($("<option></option>").val("shop").text("<?php echo $Language->getText(SHOP) ?>"));
      }
    }

    
    $.ajax({
        data: data,
        url: 'admin/modules/ajax/getLocation_translate.php',  
        success: function(result){
          //console.log(result);
          $('#location_detail').html(result);
        }
      });
  });

  $('#pickuplocation').trigger('change');

  $('#return_location').change(function(){
    
    var data = 'lang=<?php echo $language ?>&t=return&p_type=' + $(this).val();
    $.ajax({
        data: data,
        url: 'admin/modules/ajax/getLocation_translate.php',   
        success: function(result){
          //console.log(result);
          $('#return_location_detail').html(result);
        }
      });
  });

  $('#return_location').trigger('change');

  $('#txt_departure_date').datepicker({autoclose: true,disabled: true, todayHighlight: true, format: 'yyyy-mm-dd',startDate: 'today'});
  $('#txt_arrival_date').datepicker({autoclose: true,disabled: true, todayHighlight: true, format: 'yyyy-mm-dd', startDate: '<?php echo $sorder_departuredate?>'});

  /* Arrival date must be greater or equal departure date */
  <?php if($sorder_departuredate != ''){ ?>
    $('#txt_arrival_date').datepicker('setStartDate','<?php echo date( 'Y-m-d', strtotime( $sorder_departuredate . ' +1 day' ) )?>'); // from simple order
  <?php } ?>
  
  $('#txt_departure_date').datepicker().on('changeDate',function(e){
    ddate = $('#txt_departure_date').datepicker('getDate');
    d = new Date(e.date);
    d.setDate(d.getDate() + 1);
    
    $('#txt_arrival_date').datepicker('setStartDate',d);
    
    if(d.getDate() == daysInMonth((d.getMonth()+1),d.getFullYear())){
      $('#txt_arrival_date').datepicker('update',d.getFullYear() + '-' + (d.getMonth() + 2) + '-01');
    }

    // Holiday
    holiday_start = new Date(2017,0,26);
    holiday_end = new Date(2017,1,1);
    dep = parseDate($('[name="departure_date"]').val());
    arrv = parseDate($('[name="arrival_date"]').val())
    if((holiday_start <= dep && dep<= holiday_end) || (holiday_start <= arrv && arrv<= holiday_end)){
      $('#pickuplocation option[value="shop"]').attr('disabled','disabled');
      $('#return_location option[value="shop"]').attr('disabled','disabled');
    }
    else{
      $('#pickuplocation option[value="shop"]').removeAttr('disabled');
      $('#return_location option[value="shop"]').removeAttr('disabled');
    }

    // Holiday for delivery
    holiday_deliv_start = new Date(2017,0,26);
    holiday_deliv_end = new Date(2017,1,5);
    if((holiday_deliv_start <= dep && dep<= holiday_deliv_end) || (holiday_deliv_start <= arrv && arrv<= holiday_deliv_end)){
      $('#pickuplocation option[value="delivery"]').attr('disabled','disabled');
    }
    else{
      $('#pickuplocation option[value="delivery"]').removeAttr('disabled');
    }

    // check soldout
    if(isSoldout()){
      $('#txtDeparturedate').val('');
      $('#sSoldout').css('display', '');
    }
    else{
      $('#sSoldout').css('display', 'none');
    }

    if($('#pickuplocation').val() == 'shop' || $('#pickuplocation').val() == 'delivery'){
      if($('#txtDeparturedate').val() != '' || $('#txtArrivaldate').val() != ''){
        day = daydiff(parseDate($('[name="departure_date"]').val()), parseDate($('[name="arrival_date"]').val()));
        if(day < 3){
          if($('#pickuplocation').val() == 'shop'){
            alert("<?php echo $Language->getText(SHOP_3DAYS) ?>");
          }
          if($('#pickuplocation').val() == 'delivery'){
            alert("<?php echo $Language->getText(DELIVERY_3DAYS) ?>");
          }
          $('#pickuplocation').val('terminal1');
        }
      }
    }
    
    get_fwd_price();
  });

  /* Arrival date must be greater or equal departure date */
  <?php if($sorder_arrivaldate != ''){ ?>
  $('#txt_departure_date').datepicker('setEndDate','<?php echo date( 'Y-m-d', strtotime( $sorder_arrivaldate . ' -1 day' ) )?>'); // from simple order
  <?php } ?>
  
  $('#txt_arrival_date').datepicker().on('changeDate',function(e){
    d = new Date(e.date);
    d.setDate(d.getDate() - 1);
    $('#txt_departure_date').datepicker('setEndDate',d);

    // Holiday
    holiday_start = new Date(2017,0,26);
    holiday_end = new Date(2017,1,1);
    dep = parseDate($('[name="departure_date"]').val());
    arrv = parseDate($('[name="arrival_date"]').val())
    if((holiday_start <= dep && dep<= holiday_end) || (holiday_start <= arrv && arrv<= holiday_end)){
      $('#pickuplocation option[value="shop"]').attr('disabled','disabled');
      $('#return_location option[value="shop"]').attr('disabled','disabled');
    }
    else{
      $('#pickuplocation option[value="shop"]').removeAttr('disabled');
      $('#return_location option[value="shop"]').removeAttr('disabled');
    }

    // Holiday for delivery
    holiday_deliv_start = new Date(2017,0,26);
    holiday_deliv_end = new Date(2017,1,5);
    if((holiday_deliv_start <= dep && dep<= holiday_deliv_end) || (holiday_deliv_start <= arrv && arrv<= holiday_deliv_end)){
      $('#pickuplocation option[value="delivery"]').attr('disabled','disabled');
    }
    else{
      $('#pickuplocation option[value="delivery"]').removeAttr('disabled');
    }

    shop_return_close = isShopClosed($('[name="arrival_date"]').val());
    
    if($('#return_location option[value="shop"]').length == 0 && shop_return_close == false){
      $("#return_location option").eq(1).before($("<option></option>").val("shop").text("<?php echo $Language->getText(SHOP) ?>"));
    }
    else{
      $('#return_location option[value="shop"]').remove();
    }

    // check soldout
    if(isSoldout()){
      $('#txtArrivaldate').val('');
      $('#sSoldout').css('display', '');
    }
    else{
      $('#sSoldout').css('display', 'none');
    }

    if($('#pickuplocation').val() == 'shop' || $('#pickuplocation').val() == 'delivery'){
      if($('#txtDeparturedate').val() != '' || $('#txtArrivaldate').val() != ''){
        day = daydiff(parseDate($('[name="departure_date"]').val()), parseDate($('[name="arrival_date"]').val()));
        if(day < 3){
          if($('#pickuplocation').val() == 'shop'){
            alert("<?php echo $Language->getText(SHOP_3DAYS) ?>");
          }
          if($('#pickuplocation').val() == 'delivery'){
            alert("<?php echo $Language->getText(DELIVERY_3DAYS) ?>");
          }
          $('#pickuplocation').val('terminal1');
        }
      }
    }
    
    get_fwd_price();
  });

  $('#payment_type').change(function(){
    if($(this).val()== 'bank'){
      $('#bank_payment').css('display','inline');
      $('#pay_bank_info').css('display','inline');
      $('#card_payment').css('display','none');
      $('#pay_card_info').css('display','none');
      ordersummary();
    } 
    else{
      //alert('網上付款系統正在更新中！\n如需要協助請聯絡客戶服務主任。');
      
      $('#bank_payment').css('display','none');
      $('#pay_bank_info').css('display','none');
      $('#pay_card_info').css('display','inline');
      ordersummary();
    }
  });

  $('#payment_type').trigger('change');

  $('#agoda_booking_id').blur(function(){
 
    $('#agoda_verify').css('display','block');
    $('#agoda_verify_msg').css('display','none');
    
    var agoda_id = {'agoda_id': $(this).val()};
    $.support.cors = true;
    $.ajax({
        type: "post",
        data: agoda_id,
        url: 'agoda_api.php',
        success: function(result){
          booking_detail = JSON.parse(result);
          
          /* if valid agoda booking id */
          if(booking_detail['@attributes'].status == 200){
            agoda_booking = booking_detail;
            
            $('#coupon_number').attr('disabled','disabled');
            $('#payment_type option[value="card"]').remove();
            ordersummary();
            
            if(agoda_booking == null){
              $('[name="chk_discount"]').attr('checked',false);
              $('[name="chk_discount"]').removeAttr('disabled');
              $('[name="chk_more_2_devices"]').attr('checked',false);
              $('[name="chk_more_2_devices"]').removeAttr('disabled');
              $('[name="chk_discount"]').attr('checked',false);   
              $('[name="chk_discount"]').removeAttr('disabled');
              $('#coupon_number').removeAttr('disabled');
              $('#agoda_verify_msg').css('display','block');

              //$('#payment_type option[value="card"]').remove();
              //$('#payment_type option[value="bank"]').remove();
              
              if($('#payment_type option[value="card"]').length == 0){
                $('#payment_type').append('<option value="card">信用卡 (Visa/Master)</option>');
              }
              if($('#payment_type option[value="bank"]').length == 0){
                $('#payment_type').append('<option value="bank">銀行過數 (匯豐/恆生/中銀/渣打)</option>');
              }
              
            }
            
          }
          else{

            agoda_booking = null;
            $('#payment_type option[value="card"]').remove();
            $('#payment_type option[value="bank"]').remove();
            
            if($('#payment_type option[value="card"]').length == 0){
              $('#payment_type').append('<option value="card">信用卡 (Visa/Master)</option>');
            }
            if($('#payment_type option[value="bank"]').length == 0){
              $('#payment_type').append('<option value="bank">銀行過數 (匯豐/恆生/中銀/渣打)</option>');
            }
            /* Invalid Booking id */
            if($('#agoda_booking_id').val() != ''){
              $('#agoda_verify_msg').css('display','block');
              //$('#payment_type option[value="card"]').remove();
              //$('#payment_type option[value="bank"]').remove();
              if($('#payment_type option[value="card"]').length == 0){
                $('#payment_type').append('<option value="card">信用卡 (Visa/Master)</option>');
              }
              if($('#payment_type option[value="bank"]').length == 0){
                $('#payment_type').append('<option value="bank">銀行過數 (匯豐/恆生/中銀/渣打)</option>');
              }
            }

            if($('#agoda_booking_id').val() == ''){
              if($('#payment_type option[value="card"]').length == 0){
                $('#payment_type').append('<option value="card">信用卡 (Visa/Master)</option>');
              }
              if($('#payment_type option[value="bank"]').length == 0){
                $('#payment_type').append('<option value="bank">銀行過數 (匯豐/恆生/中銀/渣打)</option>');
              }
            }
            
            $('[name="chk_discount"]').attr('checked',false);
            $('[name="chk_discount"]').removeAttr('disabled');
            $('[name="chk_more_2_devices"]').attr('checked',false);
            $('[name="chk_more_2_devices"]').removeAttr('disabled');
            $('#coupon_number').removeAttr('disabled');
          }
          $('#agoda_verify').css('display','none');
          $('#payment_type').trigger('change');
          
          ordersummary();
        }
      });
  });

  $('[name="qty"], [name="internet_phone"]').change(function(){

    get_fwd_price();

    // Check available device automatically
    //var is_available = checkSoldout($('[name="country"]').val(),$('[name="device_name"]').val());
    // Check device menually
    var is_available = !(isSoldout());
    
    if(!is_available){
      $('[name="qty"]').val('');
      //$('#sSoldout').removeAttr('style');
      $('#sSoldout').attr('style','float: right;margin-right: 230px;color: red;');
      
    }
    else{
      $('#sSoldout').attr('style','float: right;margin-right: 230px;color: red;display:none');
    }
    // End check available device
      ordersummary();
    });

  $('[name="departure_date"], [name="arrival_date"]').change(function(){

    // Check available device automatically
    //var is_available = checkSoldout($('[name="country"]').val(),$('[name="device_name"]').val());
    // Check available device manually
    /*
    var is_available = !(isSoldout());
    if(!is_available){
      $('[name="qty"]').val('');
      $('#sSoldout').removeAttr('style');
    }
    else{
      $('#sSoldout').attr('style','display:none');
    }*/
    // End check available device
    
    $('#agoda_booking_id').val('');
    agoda_booking = null;
    ordersummary();
  });


  <?php 
    if($sorder_country == 'Hong Kong'){
      echo "$('#dvNightpromo').attr('style','display:none;');";
    }
  ?>
  
  $('[name="country"]').change(function(){

    get_fwd_price();

    if($('[name="country"]').val() == 'Hong Kong'){
      $('#dvNightpromo').attr('style','display:none;');
    }
    else{
      $('#dvNightpromo').removeAttr('style');
    }

    // check soldout
    if(isSoldout()){
      $('#txtDeparturedate').val('');
      $('#sSoldout').css('display', '');
    }
    else{
      $('#sSoldout').css('display', 'none');
    }
    //----------------------------------------------

    var discount_countries = ["Japan", "Korea", "Taiwan", "Thailand"];
    if(discount_countries.indexOf($(this).val()) > -1){
        $('[name="chk_discount"]').removeAttr('disabled');
      }
    else{
      //$('[name="chk_discount"]').attr('disabled',true);
    }


    var limited_countries = ["Macau" , "Singapore", "Malaysia", "Singapore+Malaysia", "Indonesia", "Vietnam", "Europe", "Philippine", "India", "Pakistan", "USA", "USA+Canada", "Canada", "Mexico", "Australia", "New Zealand", "Bangladesh", "Cambodia", "Qatar", "Russia", "Saudi Arabia", "South Africa", "Turkey", "UAE", "worldwide" ];
    var limited_except_plans = {"USA":"Verizon 4G LTE"};
    var limited_price =0;
    var sub_total = 0;
    var limited_price_per_day = 0;
    var limited_date = parseDate($('[name="departure_date"]').val());
    limited_month = limited_date.getMonth();

    var is_valid_plan = true;
    // check valid data plan with country
    if(limited_except_plans[$('[name="country"]').val()] != undefined){
      if($('[name="device_name"]').val() == limited_except_plans[$('[name="country"]').val()]){
        is_valid_plan = false;
      }
    }

    if((limited_countries.indexOf($('[name="country"]').val()) == -1) && (limited_month == 4 || limited_month == 5) && is_valid_plan){
      $('[name="chk_limited_promo"]').attr('disabled','disabled');
      $('[name="chk_limited_promo"]').attr('checked',false);
      $('#coupon_number').removeAttr('disabled');
      $('#agoda_booking_id').removeAttr('disabled');
      $('#pickuplocation option[value="shop"]').removeAttr('disabled');
      $('#pickuplocation option[value="delivery"]').removeAttr('disabled');
    }
    else{
      $('[name="chk_limited_promo"]').removeAttr('disabled','disabled');
    }
    
    
    getDeviceName($(this).val());


    

    $('[name="qty"]').prop('selectedIndex', 0);

    $('#agoda_booking_id').val('');
    if($('#payment_type option[value="card"]').length == 0){
      $('#payment_type').append('<option value="card">信用卡 (Visa/Master)</option>');
    }
    if($('#payment_type option[value="bank"]').length == 0){
      $('#payment_type').append('<option value="bank">銀行過數 (匯豐/恆生/中銀/渣打)</option>');
    }
    agoda_booking = null;
    ordersummary();
    
    /* Coupon Event */
    if($('#coupon_number').val() != ''){
      $('#coupon_number').trigger('blur');
    }

      

  });

  $('body').on('change',function(){
    // ordersummary();
  });

  function ordersummary(){

    var cost = 0;
    var phone_cost = 0;
    var phone_deposit = 0;
    var product_deposit = 0;
    var deposit =0;
    var delivery_cost = 0;
    var total = 0;
    var balance = 0;
    var days = 0;
    var qty = 0;
    var discount = 0;
    var is_discountEarlybirdOther = false;
    var discountEarlybirdOtherCountries = 0;
    var discount_device = 0;
    var discount_night = 0;
    var total_discount = 0;
    var coupon_discount = 0;
    var night_discount = 0;
    var limited_discount = 0;
    var device_discount = 0;
    var discount_4countries = 0;
    var agoda_discount = 0;
    var coupon_with_promotion = 0;
    var otherEarlyBirdDiscount = 0;
    var price_per_day = getCostPerDay();
    var HKD_rate = 7.75;

    var early_birth_discount_amount = 0;
    var cost_1 = 0;

    var insurance_total = 0;
    insurance_total = parseFloat($('#insurance-total').val());

    
    
    qty = $('[name="qty"]').val();
    /* Deposity */
    product_deposit = qty * 1000;
    
    $('#destination').html($('[name="country"] option[value!=""]:selected').text());
    $('#qty').html($('[name="qty"]').val());
    $('#d_date').html($('[name="departure_date"]').val());
    $('#a_date').html($('[name="arrival_date"]').val());
    delivery_cost = parseFloat($('#delivery_cost').html());
    
    var days = daydiff(parseDate($('[name="departure_date"]').val()), parseDate($('[name="arrival_date"]').val()));
    $('#total_days').html(days);

    // if 2days no night promotion
    if(days <= 2){
      $('[name="chk_night_promo"]').attr('checked',false);
      $('[name="chk_night_promo"]').attr('disabled','disabled');
    }
    else{
      $('[name="chk_night_promo"]').removeAttr('disabled');
    }
    
    // minus one day charge for shop and delivery
    if($('[name="pickuplocation"]').val() == 'shop' || $('[name="pickuplocation"]').val() == 'delivery'){
      days = days - 1;
    }
    //--------------
    
    

    
    if($('[name="internet_phone"]').prop('checked')){
      $('#phone_qty').html($('.i_number').val());
      phone_cost = $('.i_number').val() * 20;
      /* deposit */
      phone_deposit = $('.i_number').val() * 1000;
    }
    else {
      $('#phone_qty').html('');
      phone_cost = 0;
      phone_deposit = 0;
    }

    /* Early bird 4 countries */
    $('#s4countries_discount').html('');
    if($('[name="chk_discount"]').prop('checked')){

      
      var tdate = new Date();
      var ddate = new Date($('[name="departure_date"]').val());
      var diff_year = ddate.getFullYear() - tdate.getFullYear();
      var diff_month = (diff_year * 12) + ddate.getMonth() - tdate.getMonth();

      today = tdate.getFullYear() + "-" + (tdate.getMonth()+1) + "-" + tdate.getDate();
      diff_day = daydiff(parseDate(today),parseDate($('[name="departure_date"]').val()));
      
      /* Check discount for 4 countries */
      if($('[name="country"]').val() == 'Japan' || $('[name="country"]').val() == 'Korea' || $('[name="country"]').val() == 'Taiwan' || $('[name="country"]').val() == 'Thailand'){
          /* Check discount condition*/
          if(diff_day >= 21){
            discount = 0.2;
            early_birth_discount_amount = qty * days * price_per_day * discount;
            
            if(agoda_booking == null){
              $('#s4countries_discount').html('蕉點優惠');
            }
          }
        }
      else{ /* Early bird for Other countries */
        if(diff_day >= 21){
          is_discountEarlybirdOther = true;
          otherEarlyBirdDiscount = 10;
          if(agoda_booking == null){
            $('#s4countries_discount').html('蕉點優惠');
          }
        }
      }

    }

    /* Coupon event */
    if(coupon != null){
      /** % discount **/
    var order_start_date = $('[name="departure_date"]').val();
    var order_end_date = $('[name="arrival_date"]').val();
    console.log(order_start_date);
    console.log(order_end_date);
    console.log(coupon);
    if( coupon.min_days <= days && 
      ( 
        ( coupon.start_date == '0000-00-00' && coupon.end_date == '0000-00-00') || 
        ( coupon.start_date <= order_start_date && order_start_date <= coupon.end_date) ||
        ( coupon.start_date <= order_start_date && coupon.end_date == '0000-00-00') 

      )
    ){
      console.log("true");
      if(coupon.discount != 0){
        /* check qty */
        $('scoupon_qty').html("");
        if(parseInt(qty) > parseInt(coupon.qty)){
          coupon_qty = coupon.qty;
          $('scoupon_qty').html(coupon.qty + " devices discount only.");
          }
        else{
          coupon_qty = qty;
          }

        coupon_with_promotion = coupon.with_promotions;

        coupon_discount = coupon_qty * days * (getCostPerDay() - otherEarlyBirdDiscount - discount_device) * (coupon.discount / 100);
        
        coupon_discount = coupon_discount - (early_birth_discount_amount) * (coupon.discount / 100);
        /* Minus Early bird discount */
        if(coupon.with_promotions == 1 || coupon.with_promotions == 3 || coupon.with_promotions == 5 || coupon.with_promotions == 7){
          //coupon_discount = coupon_discount * ( 1 - discount);
        
          //$('[name="chk_discount"]').attr('checked',false);
          $('[name="chk_discount"]').removeAttr('disabled');
        }
        else{
          discount = 0;
          $('[name="chk_discount"]').attr('checked',false);
          $('[name="chk_discount"]').attr('disabled','disabled');
        }
        /* END Minus Early bird discount */
        total_discount =  coupon_discount;
        
        if(agoda_booking == null){
          $('#scoupon_discount').html(coupon.description);
        }
      }
      /** day discount **/
      if(coupon.num_days != 0){
        cday = (days < coupon.num_days) ? days : coupon.num_days;

        if(parseInt(qty) > parseInt(coupon.qty)){
          coupon_qty = coupon.qty;
          $('scoupon_qty').html(coupon.qty + " devices discount only.");
          }
        else{
          coupon_qty = qty;
          }
        
        coupon_with_promotion = coupon.with_promotions;
        
        coupon_discount = coupon_qty * (getCostPerDay() - otherEarlyBirdDiscount) * cday;
        coupon_discount = coupon_discount - (early_birth_discount_amount) * (coupon.discount / 100);
        /* Minus Early bird discount */
        if(coupon.with_promotions == 1 || coupon.with_promotions == 3 || coupon.with_promotions == 5 || coupon.with_promotions == 7){
          coupon_discount = coupon_discount * ( 1 - discount);
          //$('[name="chk_discount"]').attr('checked',false);
          $('[name="chk_discount"]').removeAttr('disabled');
        }
        else{
          discount = 0;
          $('[name="chk_discount"]').attr('checked',false);
          $('[name="chk_discount"]').attr('disabled','disabled');
        }
        /* END Minus Early bird discount */
        total_discount =  coupon_discount;
        if(agoda_booking == null){
          $('#scoupon_discount').html(coupon.description);
        }
      }

      /** amount discount **/
      
      if(coupon.amount != 0){
        
        coupon_with_promotion = coupon.with_promotions;
        
        coupon_discount = parseFloat(coupon.amount);
        /* Minus Early bird discount */
        if(coupon.with_promotions == 1 || coupon.with_promotions == 3 || coupon.with_promotions == 5 || coupon.with_promotions == 7){
          //$('[name="chk_discount"]').attr('checked',false); 
          $('[name="chk_discount"]').removeAttr('disabled');
        }
        else{
          discount = 0;
          $('[name="chk_discount"]').attr('checked',false);
          $('[name="chk_discount"]').attr('disabled','disabled');
        }
        /* END Minus Early bird discount */
        total_discount =  coupon_discount;
        if(agoda_booking == null){
          $('#scoupon_discount').html(coupon.description);
        }
      }

      if(coupon.num_days == 0 && coupon.discount == 0 && coupon.amount == 0){
        coupon_discount = 0;
        if(agoda_booking == null){
          $('#scoupon_discount').html(coupon.description);
          $('[name="chk_discount"]').removeAttr('disabled');
          $('[name="chk_more_2_devices"]').removeAttr('disabled');
          $('[name="chk_night_promo"]').removeAttr('disabled');
          coupon_with_promotion = coupon.with_promotions;
        }
      }
}
    }else{
      $('#scoupon_discount').html('');
      $('#pickuplocation').removeAttr('disabled');
      $('#return_location').removeAttr('disabled');
      
    }
    /*\\-----Coupon---------------*/


    /* More than 2 devices discount */
    $('#s2devices_discount').html('');
    if($('[name="chk_more_2_devices"]').prop('checked')){
      
      /* Check discount for 4 countries */
      if($('[name="country"]').val() == 'Japan' || $('[name="country"]').val() == 'Korea' || $('[name="country"]').val() == 'Taiwan' || $('[name="country"]').val() == 'Thailand' ){
          /* Check discount condition*/
          if($('[name="qty"]').val() > 1){
            discount_device = 5;
            if(agoda_booking == null){
              $('#s2devices_discount').html('孖住上優惠');
            }
          }
      }

    }

    /* Night promotion */
    $('#snight_discount').html('');
    if($('[name="chk_night_promo"]').prop('checked')){
      
        /* Check discount condition*/
        hour = $('[name="phour"]').val();
        minute = $('[name="pminute"]').val();

        if(parseInt(hour) >=19){
          if(days > 1){
            discount_night = 1; // one day discount
            $('#snight_discount').html('夜間時段取機優惠');
          }
        }
    }

    

    /* Deposit */
    if($('[name="payment_type"]').val() != 'card' && $('[name="payment_type"]').val() != null){
      deposit = phone_deposit + product_deposit;
    }
    else{
      deposit = 0;
    }
    
    var c_discount = 0; 
    if(coupon!=null) {
      c_days = coupon.num_days;
      c_discount = coupon.discount / 100;
    }
    else {c_days = 0;}

    var r_days = 0;
    r_days = (days <= c_days? 0: (days - c_days));
    
    cost = days * qty * getCostPerDay();
    
    /* coupon with two device promotion */
    if(coupon_with_promotion == 2 || coupon_with_promotion == 3 || coupon_with_promotion == 6 || coupon_with_promotion == 7){
      device_discount = (discount_device * qty * (r_days));
      
      $('[name="chk_more_2_devices"]').removeAttr('disabled');
      //$('[name="chk_more_2_devices"]').attr('checked',false);
    }
    else{
      if(coupon != null){
        device_discount = 0;
        $('[name="chk_more_2_devices"]').attr('checked',false);
        $('[name="chk_more_2_devices"]').attr('disabled','disabled');
      }
      else{
          device_discount = (discount_device * qty * (r_days));
      }
    }
    /* END coupon with two device promotion */

    night_discount = discount_night * qty * (getCostPerDay() - otherEarlyBirdDiscount - discount_device);
    night_discount = night_discount * (1 - (discount)); // night discount with 4 countries departure 2 months before 
    night_discount = night_discount * (1 - (c_discount)); //  - coupon discount
    discount_4countries = cost * discount;

    /* Early bird for other countries */
    if(is_discountEarlybirdOther == true){
      if((coupon_with_promotion == 1 || coupon_with_promotion == 3 || coupon_with_promotion == 5 || coupon_with_promotion == 7)){
        discountEarlybirdOtherCountries =  days * qty * 10;
      }
      else{
        if(coupon != null){
          discountEarlybirdOtherCountries = 0;
        }
        else{
          discountEarlybirdOtherCountries =  days * qty * 10;
        }
      }
      
       if(night_discount > 0){
        //night_discount = night_discount - (qty * 10);
       }
    }

    /* coupon with night promotion */
    if((coupon_with_promotion == 4 || coupon_with_promotion == 5 || coupon_with_promotion == 6 || coupon_with_promotion == 7)){ /* promotion is included */
      //$('[name="chk_night_promo"]').attr('checked',false);
      $('[name="chk_night_promo"]').removeAttr('disabled');
    }
    else{ /* night promotion is not included */
      if(coupon != null){
        night_discount = 0;
        $('[name="chk_night_promo"]').attr('checked',false);
        $('[name="chk_night_promo"]').attr('disabled','disabled');
      }
    }
    /* END coupon with night promotion */
    
    if(r_days == 0){
      device_discount = 0;
      night_discount = 0;
      discount_4countries = 0;
    }
    if(r_days == 1 && night_discount > 0){
      device_discount = 0;
    }


    /*------------- Limited Promotion ------------------------ */
    $("#slimited_discount").html('');
    if($('[name="chk_limited_promo"]').prop('checked')){
      
      var limited_countries = ["Macau" , "Singapore", "Malaysia", "Singapore+Malaysia", "Indonesia", "Vietnam", "Europe", "Philippine", "India", "Pakistan", "USA", "USA+Canada", "Canada", "Mexico", "Australia", "New Zealand", "Bangladesh", "Cambodia", "Qatar", "Russia", "Saudi Arabia", "South Africa", "Turkey", "UAE", "worldwide" ];
      var limited_except_plans = {"USA":"Verizon 4G LTE"};
      var limited_price =0;
      var sub_total = 0;
      var limited_price_per_day = 0;
      var limited_date = parseDate($('[name="departure_date"]').val());
      limited_month = limited_date.getMonth();

      var is_valid_plan = true;

      // check valid data plan with country
      if(limited_except_plans[$('[name="country"]').val()] != undefined){
        if($('[name="device_name"]').val() == limited_except_plans[$('[name="country"]').val()]){
          is_valid_plan = false;
        }
      }

      if(qty > 1 && (limited_countries.indexOf($('[name="country"]').val()) > -1) && (limited_month == 4 || limited_month == 5) && is_valid_plan){

        sub_total = cost - night_discount -  discountEarlybirdOtherCountries;
        limited_price_per_day = sub_total / (qty * (days - discount_night));
  
        limited_price = parseInt(qty / 2) * (days - discount_night) * 30 + Math.ceil(qty / 2) * limited_price_per_day * (days - discount_night); // odd + even
        
  
        // Calculate limited discount
        limited_discount = sub_total - limited_price;
  
        $("#slimited_discount").html('Limited discount');
      }
      
    }
    /***---------------End Limited Promotion------------------------***/
  
    total_discount = total_discount + (cost * (promotion_discount/100) + discount_4countries) + device_discount + night_discount + discountEarlybirdOtherCountries + limited_discount;

    /* Agoda Coupon */
    $('#s5agoda_discount').html('');
    if(agoda_booking != null){
      
            
      array_countries = ["Japan","Korea","China","Taiwan","Hong Kong", "Macau", "Thailand", "Vietnam", "Philippines","Cambodia","Singapore", "Malaysia", "Indonesia"];    
      array_countries2 = ["USA","Canada","Mexico","Australia","Russia", "UAE", "Europe"];
      european_countries = ["Albania","Andorra","Austria","Azerbaijan","Belarus","Belgium","Bosnia Herzegovina","Bulgaria","Croatia","Cyprus","Czech Republic","Denmark","Estonia","Faroe Islands","Finland","France","Georgia","Germany","Gibraltar","Greece","Greenland","Guernsey","Hungary","Iceland","Ireland","Isle Of Man","Italy","Jersey","Kosovo","Latvia","Liechtenstein","Lithuania","Luxembourg","Macedonia","Malta","Moldova","Monaco","Montenegro","Netherlands","Norway","Poland","Portugal","Romania","Russia","San Marino","Serbia","Slovakia","Slovenia","Spain","Sweden","Switzerland","Turkey","Ukraine","United Kingdom"];
        
      /* Check discount for 13 countries */
      agoda_country = agoda_booking.Bookings.Booking.Country;
      checkInDate = agoda_booking.Bookings.Booking.CheckInDate;
      checkOutDate = agoda_booking.Bookings.Booking.CheckOutDate;
      depDate = $('[name="departure_date"]').val();
      arrivalDate = $('[name="arrival_date"]').val();
      
      d_checkInDate = parseDate(checkInDate);
      d_checkOutDate = parseDate(checkOutDate);
      d_depDate = parseDate(depDate);
      d_arrivalDate = parseDate(arrivalDate);
      
      d_today = new Date();

      /* agoda country rename */
      if(agoda_country == 'South Korea'){
        agoda_country = 'Korea';
      }

      if(agoda_country == 'United States of America'){
        agoda_country = 'USA';
      }

      if(agoda_country == 'United Arab Emirates'){
        agoda_country = 'UAE';
      }

      if(european_countries.indexOf(agoda_country) > -1){
        agoda_country = 'Europe';
      }

      /* End agoda country rename */

      var country_name = "";
      if($('[name="country"]').val() != null){
        country_name = $('[name="country"]').val();
      }
      
      if((country_name.indexOf(agoda_country) > -1) && (d_depDate <= d_checkInDate) && (d_checkInDate <= d_arrivalDate) && (d_arrivalDate >= d_checkOutDate) && (d_depDate <= d_checkOutDate)){ 
      //if((country_name.indexOf(agoda_country) > -1) && (d_today < d_checkInDate)){

        /* 1st promotion areas */
        if(array_countries.indexOf(agoda_country) != -1){

          if(agoda_booking.Bookings.Booking.Status != "BookingCharged"){
            payment = 0;
          }
          else{
            payment = agoda_booking.Bookings.Booking.TotalRateUSD["@attributes"].inclusive * HKD_rate;    
          }

          /* payment based amount 500 */
          if(payment > 500){
            int_pay = parseInt(payment / 500);
            //int_pay = int_pay * 500;
            agoda_days = daydiff(d_checkInDate, d_checkOutDate);
            p_price = getCostPerDay();
            
            agoda_discount = int_pay * p_price;

            if(discount_night == 1){
              night_discount = p_price * qty;
            }
          
            total_discount = parseFloat(agoda_discount) + parseFloat(night_discount);
            
            if(total_discount > cost) {
              agoda_discount = cost - night_discount;
              //night_discount = 0;
              total_discount = agoda_discount + night_discount;
            }
            else{
              
            }
            
            $('#s5agoda_discount').html('Agoda');
            /* reset other promotion */ 
            coupon_discount = 0;  
            device_discount = 0;
            discount_4countries = 0;
            
            
            $('[name="chk_discount"]').attr('checked',false);
            $('[name="chk_discount"]').attr('disabled','disabled');
            $('[name="chk_more_2_devices"]').attr('checked',false);
            $('[name="chk_more_2_devices"]').attr('disabled','disabled');

            //$('#payment_type option[value="card"]').remove();
            if($('#payment_type option[value="bank"]').length == 0){
              $('#payment_type').append('<option value="bank">銀行過數 (匯豐/恆生/中銀/渣打)</option>');
            }
          }
          else{
            agoda_booking = null;
          }
        }

        /*2nd promotion areas */
        else if(array_countries2.indexOf(agoda_country) != -1){
          
          if(agoda_booking.Bookings.Booking.Status != "BookingCharged"){
            payment = 0;
          }
          else{
            payment = agoda_booking.Bookings.Booking.TotalRateUSD["@attributes"].inclusive * HKD_rate;    
          }

          
          /* payment based amount 1000 */
          if(payment > 1500){
            int_pay = parseInt(payment / 1000);
            //int_pay = int_pay * 500;
            agoda_days = daydiff(d_checkInDate, d_checkOutDate);
            p_price = getCostPerDay();
            
            agoda_discount = int_pay * p_price;

            if(discount_night == 1){
              night_discount = p_price * qty;
            }
          
            total_discount = parseFloat(agoda_discount) + parseFloat(night_discount);
            if(total_discount > cost) {
              agoda_discount = cost - night_discount;
              //night_discount = 0;
              total_discount = agoda_discount + night_discount;
            }
            else{
              
            }
          
            total_discount = agoda_discount + night_discount;
            $('#s5agoda_discount').html('Agoda');
            
            /* reset other promotion */ 
            coupon_discount = 0;  
            device_discount = 0;
            discount_4countries = 0;
            
            $('[name="chk_discount"]').attr('checked',false);
            $('[name="chk_discount"]').attr('disabled','disabled');
            $('[name="chk_more_2_devices"]').attr('checked',false);
            $('[name="chk_more_2_devices"]').attr('disabled','disabled');

            $('#payment_type option[value="card"]').remove();
            
            /*
            if($('#payment_type option[value="bank"]').length == 0){
              $('#payment_type').append('<option value="bank">銀行過數 (匯豐/恆生/中銀/渣打)</option>');
            }*/
          }
          else{
            agoda_booking = null;
          }
        }
        else{
          agoda_booking = null;
        }
        
      }
      else{
        agoda_booking = null;
      }
    }

  
    /* \End Agoda discount */
    
    total_phone_cost = phone_cost * days;
    total = delivery_cost + cost + deposit + total_phone_cost - total_discount + insurance_total; 

    // prevent minus
    if((total - deposit) <= 0){
      total = deposit;
      // disable card payment
      $('#payment_type option[value="card"]').attr('disabled','disabled');
    }
    else{
      // enable card payment
      $('#payment_type option[value="card"]').removeAttr('disabled');
    }


    $('#cost').html(isNaN(cost)?0: cost);
    $('#phone_cost').html(isNaN(total_phone_cost)?0: total_phone_cost);
    $('#total_cost').html(isNaN(total)?0: total.toFixed(1));
    $('#deposit').html(isNaN(deposit)?0:deposit);
    
    $('#discount').html((coupon_discount == 0 || isNaN(coupon_discount))?'': '-' + coupon_discount.toFixed(1));   
    $('#discount2').html((device_discount == 0 || isNaN(device_discount))?'': '-' + device_discount.toFixed(1));
    $('#discount3').html((night_discount == 0 || isNaN(night_discount))?'': '-' + night_discount.toFixed(1));
    $('#discount6').html((limited_discount == 0 || isNaN(limited_discount))?'': '-' + limited_discount.toFixed(1));

    if(is_discountEarlybirdOther == false){
      $('#discount_4').html((discount_4countries == 0 || isNaN(discount_4countries))?'': '-' + discount_4countries.toFixed(1));
    }
    else{
      $('#discount_4').html((discountEarlybirdOtherCountries == 0 || isNaN(discountEarlybirdOtherCountries))?'': '-' + discountEarlybirdOtherCountries.toFixed(1));
    }
    
    $('#discount5').html((agoda_discount == 0 || isNaN(agoda_discount))?'': '-' + agoda_discount.toFixed(1));   

    $('#product_desc').html($('[name="device_name"]').val());     
    
    
  }

  $('#coupon_number').blur(function(){

    if($(this).val() == '') {
      $('#scoupon_message').html('');
    }
  
    $('[name="chk_more_2_devices"]').removeAttr('disabled');
    
    var data = 'coupon_num=' + $(this).val() + "&coupon_type=" + $("#coupon_type").val();
    var url = 'admin/modules/ajax/getCoupon.php';

    $.ajax({
      data: data,
      type: 'post',
      url: url,
      success: function(result){
        c = JSON.parse(result);
        if(c.length > 0){ 
          coupon = c[0];
          
          // All countries
          if(coupon.country == ''){

          }
          else { // specific country
            c_countries = coupon.country.split(";");
            if(c_countries.indexOf($('[name="country"]').val()) < 0){
              coupon = null;
              if($('#coupon_number').val() != ''){
                $('#scoupon_message').html('Invalid coupon.');
              }
            }
          }

          /* Initailize enable more than 2 devices promotion */
            $('[name="chk_more_2_devices"]').attr('checked',false);
            $('[name="chk_more_2_devices"]').removeAttr('disabled');

          if(coupon !=null){
            $('#scoupon_message').html('');
            /* pickup location */
            c_pickuplocation = coupon.pickuplocation.split(";");
            
            // Terminal1
            if(c_pickuplocation.indexOf("terminal1") >= 0){
              $('#pickuplocation option[value="shop"]').attr('disabled','disabled');
              $('#pickuplocation option[value="delivery"]').attr('disabled','disabled');
              $('#pickuplocation option[value="terminal2"]').attr('disabled','disabled');
            }

            // Terminal2
            if(c_pickuplocation.indexOf("terminal2") >= 0){
              $('#pickuplocation option[value="shop"]').attr('disabled','disabled');
              $('#pickuplocation option[value="delivery"]').attr('disabled','disabled');
              $('#pickuplocation option[value="terminal1"]').attr('disabled','disabled');
              if(c_pickuplocation.indexOf("terminal1") >= 0){
                $('#pickuplocation option[value="terminal1"]').removeAttr('disabled');
              }
              $('#pickuplocation option[value="terminal2"]').removeAttr('disabled');
            }

            /* return location */
            c_returnlocation = coupon.returnlocation.split(";");
            
            // Terminal1
            if(c_returnlocation.indexOf("terminal1") >= 0){
              $('#return_location option[value="shop"]').attr('disabled','disabled');
              $('#return_location option[value="terminal2"]').attr('disabled','disabled');
            }

            // Terminal2
            if(c_returnlocation.indexOf("terminal2") >= 0){
              $('#return_location option[value="shop"]').attr('disabled','disabled');
              $('#return_location option[value="terminal1"]').attr('disabled','disabled');
              if(c_returnlocation.indexOf("terminal1") >= 0){
                $('#return_location option[value="terminal1"]').removeAttr('disabled');
              }
              $('#return_location option[value="terminal2"]').removeAttr('disabled');
            }
            
            /* disable more than 2 devices promotion */
            early_promo_code = [1,3,5,7];
            twin_promo_code = [2,3,6,7];
            night_promo_code = [4,5,6,7];
            if(early_promo_code.indexOf(parseInt(coupon.with_promotions)) < 0){
              $('[name="chk_discount"]').attr('checked',false);
              $('[name="chk_discount"]').attr('disabled','disabled');
            }
            if(twin_promo_code.indexOf(parseInt(coupon.with_promotions)) < 0){
              $('[name="chk_more_2_devices"]').attr('checked',false);
              $('[name="chk_more_2_devices"]').attr('disabled','disabled');
            }
            if(night_promo_code.indexOf(parseInt(coupon.with_promotions)) < 0){
              $('[name="chk_night_promo"]').attr('checked',false);
              $('[name="chk_night_promo"]').attr('disabled','disabled');
            }
          }
          
          
          /*\-------------------------------*/
          
          ordersummary();
          
        }
        else{
          coupon = null;
          ordersummary();
          if($('#coupon_number').val() != ''){
            $('#scoupon_message').html('Invalid coupon.');
          }
          $('#pickuplocation').removeAttr('disabled');
          $('#pickuplocation option[value="shop"]').removeAttr('disabled');
          $('#pickuplocation option[value="delivery"]').removeAttr('disabled');
          $('#pickuplocation option[value="terminal1"]').removeAttr('disabled');
          $('#pickuplocation option[value="terminal2"]').removeAttr('disabled');
          
          $('#return_location').removeAttr('disabled');
          $('#return_location option[value="shop"]').removeAttr('disabled');
          $('#return_location option[value="terminal1"]').removeAttr('disabled');
          $('#return_location option[value="terminal2"]').removeAttr('disabled');
        }
        get_fwd_price();
      }
    });

  });

  $('#coupon_type').change(function(){
    $('#coupon_number').val('');
    
    
    if($('[name="chk_more_2_devices"]').prop("checked")){
      if($('#coupon_type').val() == "axa"){
        $('#coupon_number').removeAttr('disabled');
      }
      else{
        $('#coupon_number').attr('disabled','disabled');
      }
    }
    
    coupon = null;
  });
  
  function getCostPerDay(){

    var url = 'admin/modules/ajax/getCost.php';
    var data = {'country':$('[name="country"]').val(),'mno' : $('[name="device_name"]').val()};
    
    var cost = 0;
    $.ajax({
        url: url,
        type: 'post',
        data: data,
        async: false,
        success: function(data){
          cost = data;
        }
      });
    return cost;
  }

  function getDeviceName(country){

    var url = 'admin/modules/ajax/getDevice.php';
    var data;
    if(country){
      //
      data = {'lang':'en','country': country, 'device_name': '<?php echo $_REQUEST["device_name"] ?>'};
    }
    else{
      //
      data = {'lang':'en','country': '<?php echo $_REQUEST['country'] ?>', 'device_name': '<?php echo $_REQUEST["device_name"] ?>'};
      }
    $.ajax({
        url: url,
        data: data,
        type: 'post',
        success: function(data){
          $('#opt_device_name').html(data);
          if($('[name="device_name"] option').size() == 2){
            $('[name="device_name"]').prop('selectedIndex', 1);
            $('#product_desc').html($('[name="device_name"]').val()); 
            loadDailyPlanMno(country,$('[name="device_name"]').val());
            $('[name="qty"]').val('1');
            
            if($('[name="departure_date"]').val() != '' && $('[name="arrival_date"]').val() != ''){
              $("#device_name").trigger('change');
            }
            
          }
          else{
            $('#product_desc').html('');  
            loadDailyPlan();
            
          }
        }
      }); 
  }


  function isSoldout(){

    var url = 'admin/modules/ajax/getSoldout.php';
    var data = {'country':$('[name="country"]').val(),'departure_date' : $('#txtDeparturedate').val(),'arrival_date' : $('#txtArrivaldate').val(), 'mno' : $('#device_name').val()};
    var is_soldout = false;   
    $.ajax({
        url: url,
        type: 'post',
        data: data,
        async: false,
        success: function(response){
          result = $.parseJSON(response);
          if(result.is_soldout){
            is_soldout = true;
          }else{
            is_soldout = false;
          }
        }
      });
    return is_soldout;
    //return false;
  }

  function isSoldoutMno(mno){

    var url = 'admin/modules/ajax/getSoldout.php';
    var data = {'country':$('[name="country"]').val(),'departure_date' : $('#txtDeparturedate').val(),'arrival_date' : $('#txtArrivaldate').val(), 'mno' : mno};
    var is_soldout = false;   
    $.ajax({
        url: url,
        type: 'post',
        data: data,
        async: false,
        success: function(response){
          result = $.parseJSON(response);
          if(result.is_soldout){
            is_soldout = true;
          }else{
            is_soldout = false;
          }
        }
      });
    return is_soldout;
    //return false;
  }
  
  <?php 
  if($sorder_country != "" && $sorder_departuredate != "" && $sdevice_name != ""){
  ?>
  // check soldout
  if(isSoldoutMno('<?php echo $sdevice_name ?>')){
    $('#txtDeparturedate').val('');
    $('#sSoldout').css('display', '');
  }
  else{
    $('#sSoldout').css('display', 'none');
  }
  //----------------------------------
    
  <?php }?>

  $(document).on('change','[name="device_name"]',function(){

    // check soldout
    if(isSoldout()){
      $('#txtDeparturedate').val('');
      $('#sSoldout').css('display', '');
    }
    else{
      $('#sSoldout').css('display', 'none');
    }
    //----------------------------------
    
    var url = 'admin/modules/ajax/getDataPlan.php';
    var data = {'country':$('[name="country"]').val(),'mno' : $('[name="device_name"]').val()};
    $.ajax({
      url: url,
      data: data,
      type: 'post',
      
      success: function(data){
        
        $('#product_dailyplan').html(data);
      }
    }); 
    /*
    is_available = checkSoldout($('[name="country"]').val(),$('[name="device_name"]').val());
    if(!is_available){
      $('[name="qty"]').val('');
      $('#sSoldout').removeAttr('style');
    }
    else{
      $('#sSoldout').attr('style','display:none');
    }*/

    $('[name="qty"]').val('1');
    ordersummary();
  });

  loadDailyPlan();
  function loadDailyPlan(){
    
    var url = 'admin/modules/ajax/getDataPlan.php';
    var data = {'country':'<?php echo $_REQUEST['country'] ?>','mno' : '<?php echo $_REQUEST['device_name'] ?>'};
    $.ajax({
      url: url,
      data: data,
      type: 'post',
      async: false,
      success: function(data){
        $('#product_dailyplan').html(data);
      
      }
    }); 
    
  }

  function loadDailyPlanMno(country,mno){
    
    var url = 'admin/modules/ajax/getDataPlan.php';
    var data = {'country':country,'mno' : mno};
    $.ajax({
      url: url,
      data: data,
      type: 'post',
      async: false,
      success: function(data){
        $('#product_dailyplan').html(data);
      
      }
    }); 
    
  }


  function checkSoldout(country,mno){

    var is_available = false;
    var url = 'admin/modules/ajax/getSoldout_new.php';
    var d_date = $('[name="departure_date"]').val();
    var a_date = $('[name="arrival_date"]').val();
    var data = {'country':country,'mno' : mno, 'departure_date': d_date, 'arrival_date': a_date};
    $.ajax({
      url: url,
      data: data,
      async: false,
      success: function(response){
        result = $.parseJSON(response);
        if(result.is_available){
          if(result.qty >= parseInt($('[name="qty"]').val())){
            is_available = true;
          }
        }

        $('[name="mno_id"]').val(result.mid);
        
      }
    }); 

    return is_available;
  }


  $('.i_sign_minus').click(function(){
    var val = $('.i_number').val();
    if(val > 1){
      val--;
      $('.i_number').val(val);
      ordersummary();
    }
  });

  $('.i_sign_plus').click(function(){
    var val = $('.i_number').val();
    if(val < 10){
      val++;
      $('.i_number').val(val);
      ordersummary();
    }
  });

  $('[name="chk_discount"]').change(function(){
    get_fwd_price();
    ordersummary();
    if($('[name="chk_discount"]').prop('checked')){

      /* disable more than 2 devices promotion */
      
      $('[name="chk_more_2_devices"]').attr('checked',false);

      if(!$('[name="chk_limited_promo"]').prop('checked')){
        $('#coupon_number').removeAttr('disabled');
      }
      
      $('[name="chk_more_2_devices"]').attr('disabled','disabled');
      $('#2devices_promotion_msg').css('display','none');
    }
    else{
      if(coupon == null && !$('[name="chk_limited_promo"]').prop('checked')){
        $('[name="chk_more_2_devices"]').removeAttr('disabled');
      }
      
    }
  });

  $('[name="chk_limited_promo"]').change(function(){
    ordersummary();
    if($('[name="chk_limited_promo"]').prop('checked')){

      alert('<?php echo $Language->getText(LIMITED_PROMOTION_CONFIRM) ?>'); 
      /* disable more than 2 devices promotion */
      
      $('[name="chk_more_2_devices"]').attr('checked',false);
      $('[name="chk_discount"]').removeAttr('disabled');
      
      $('[name="chk_more_2_devices"]').attr('disabled','disabled');
      $('#coupon_number').attr('disabled','disabled');
      $('#coupon_number').val('');
      $('#agoda_booking_id').attr('disabled','disabled');
      $('#agoda_booking_id').val('');
      $('#2devices_promotion_msg').css('display','none');
      coupon = null;
      agoda_booking = null;

      $('#pickuplocation option[value="shop"]').attr('disabled','disabled');
      $('#pickuplocation option[value="delivery"]').attr('disabled','disabled');
    }
    else{
      if(!$('[name="chk_discount"]').prop('checked')){
        $('[name="chk_more_2_devices"]').removeAttr('disabled');
      }
      $('#coupon_number').removeAttr('disabled');
      $('#agoda_booking_id').removeAttr('disabled');
      $('#pickuplocation option[value="shop"]').removeAttr('disabled');
      $('#pickuplocation option[value="delivery"]').removeAttr('disabled');
    }
  });

  $('[name="chk_more_2_devices"]').change(function(){
    get_fwd_price();
    ordersummary();
    if($('[name="chk_more_2_devices"]').prop('checked')){

      /* disable more than 2 devices promotion */
      $('[name="chk_discount"]').attr('checked',false);
      $('[name="chk_discount"]').attr('disabled','disabled');
      $('[name="chk_limited_promo"]').attr('checked',false);
      $('[name="chk_limited_promo"]').attr('disabled','disabled');
      $('#coupon_number').attr('disabled','disabled');
      $('#2devices_promotion_msg').css('display','block');
    }
    else{
      $('[name="chk_discount"]').removeAttr('disabled');
      $('[name="chk_limited_promo"]').removeAttr('disabled');
      $('#coupon_number').removeAttr('disabled');
      $('#2devices_promotion_msg').css('display','none');
    }
  });

  $('[name="chk_night_promo"]').change(function(){
    get_fwd_price();
  });

  /* Pay by credit card */
  function card_payment(){

    /*URL For Test Credit Card*/
    var pay_url = 'https://test.paydollar.com/b2cDemo/eng/merchant/api/orderApi.jsp';

    /*URL For Real Env */
    //var pay_url = 'https://www.paydollar.com/b2c2/eng/payment/payForm.jsp';
    
/*
    var card_type = $('[name="card_type"]:checked').val();
    var card_number = $('[name="card_number"]').val();
    var card_expire_month = $('[name="card_expire_month"]').val();
    var card_expire_year = $('[name="card_expire_year"]').val();
    var card_security_code = $('[name="card_security_code"]').val();
*/
    var card_type = 'VISA';
    var card_number = '4335900000140045';
    var card_expire_month = '07';
    var card_expire_year = '2020';
    var card_security_code = '123';

    var merchantId = 'merchantId=88129010';//88593999';
    var loginId = '&loginId=admin';
    var password ='&password=bw82076699'; //bw82076699';
    var amount = '&amount=1';
    var actionType = '&actionType=Capture';
    var orderRef = '&orderRef=000000000032';
    var currCode = '&currCode=344';
    var pMethod = '&pMethod=VISA'; // VISA, Master
    var cardHolder = '&cardHolder=testing';
    var cardNo = '&cardNo=' + card_number;
    var epMonth = '&epMonth=' + card_expire_month;
    var epYear = '&epYear=' + card_expire_year;
    var securityCode = '&securityCode=' + card_security_code;
    var payType = '&payType=N';
    var successUrl = '&successUrl=http://bananawifi.com/pay_card_success.php';
    var failUrl = '&failUrl=http://bananawifi.com/pay_card_fail.php';
    var errorUrl = '&errorUrl=http://bananawifi.com/pay_card_error.php';
    var lang = '&lang=E';
    var secureHash = '&secureHash=44f3760c201d3688440f62497736bfa2aadd1bc0';

    var data = merchantId + amount + actionType + password + loginId + orderRef + currCode + pMethod + cardHolder + cardNo + epMonth + epYear + securityCode + payType; //+ successUrl + failUrl + errorUrl + lang + secureHash;
    
    $.ajax({
        url: pay_url,
        type: 'POST',
        dataType:'jsonp',
          crossDomain:true,
        data: data,
        
        success: function(result){
          // error message
        
        }
        
      });

  }

  $('#testpay').click(function(){
    //card_payment();
    $('#payForm').submit();
  });

  function submitOrder(){
    var u = '<?php echo $uid;?>';
    var deposit = parseFloat($('#deposit').html());
    var delivery_cost = parseFloat($('#delivery_cost').html());
    var days = daydiff(parseDate($('[name="departure_date"]').val()), parseDate($('[name="arrival_date"]').val()));
    var phone_qty = 0;
    var phone_cost;
    var discount_amount = 0;
    var discount = 0;
    var is_discountEarlybirdOther = false;
    var discountEarlybirdOtherCountries = 0;
    var discount_device = 0;
    var discount_night = 0; 
    var device_discount = 0;
    var night_discount = 0;
    var limited_discount = 0;
    var discount_4countries = 0;
    var coupon_discount = 0;
    var agoda_discount = 0;
    var agoda_desc = '';
    var coupon_with_promotion = 0;
    var otherEarlyBirdDiscount = 0;
    var HKD_rate = 7.75;

    var early_birth_discount_amount = 0;

    var price_per_day = getCostPerDay();
    var qty = $('[name="qty"]').val();

    // minus one day charge for shop and delivery
    if($('[name="pickuplocation"]').val() == 'shop' || $('[name="pickuplocation"]').val() == 'delivery'){
      days = days - 1;
    }
    //----------------------------------
    
    if($('[name="internet_phone"]').prop('checked')){
      phone_cost = $('.i_number').val() * 20 * days;
      phone_qty = $('.i_number').val();
    }
    else {
      phone_cost = 0;
      phone_qty = 0;
    }

    var promo_desc = '';

    /* Check discount */
    if($('[name="chk_discount"]').prop('checked')){
      var tdate = new Date();
      var ddate = new Date($('[name="departure_date"]').val());
      var diff_year = ddate.getFullYear() - tdate.getFullYear();
      var diff_month = (diff_year * 12) + ddate.getMonth() - tdate.getMonth();

      today = tdate.getFullYear() + "-" + (tdate.getMonth()+1) + "-" + tdate.getDate();
      diff_day = daydiff(parseDate(today),parseDate($('[name="departure_date"]').val()));
      /* Check discount for 4 countries */
      if($('[name="country"]').val() == 'Japan' || $('[name="country"]').val() == 'Korea' || $('[name="country"]').val() == 'Taiwan' || $('[name="country"]').val() == 'Thailand'){
          /* Check discount condition*/
          if(diff_day >= 21){
            discount = 0.2;
            early_birth_discount_amount = qty * price_per_day * days * discount;
            promo_desc = ' / 蕉點優惠';
          }
        }
      else{
        /* Check discount condition*/
        if(diff_day >= 21){
          is_discountEarlybirdOther = true;
          otherEarlyBirdDiscount = 10;
          promo_desc = ' / 蕉點優惠';
        }
      
      }
    }

    /* Coupon event */
    var coupon_code = '';
    var coupon_desc = '';
    var coupon_unlimited = 0;
    if(coupon != null){
      /** % discount **/
      if(coupon.discount != 0){
        /* check qty */
        if(parseInt(qty) > parseInt(coupon.qty)){
          coupon_qty = coupon.qty;
          }
        else{
          coupon_qty = qty;
          }

        coupon_with_promotion = coupon.with_promotions;
        
        coupon_discount = coupon_qty * days * (price_per_day - otherEarlyBirdDiscount - discount_device) * (coupon.discount / 100);
        
        
        /* Minus Early bird discount */
        if(coupon.with_promotions == 1 || coupon.with_promotions == 3 || coupon.with_promotions == 5 || coupon.with_promotions == 7){
          coupon_discount = coupon_discount * ( 1 - discount);
        }
        else{
          discount = 0;
        }
        /* END Minus Early bird discount */
        
        discount_amount = coupon_discount;
        coupon_code = coupon.coupon_num;
        coupon_desc = coupon.description;
        coupon_unlimited = coupon.is_unlimited;
      }
      
      if(coupon.num_days != 0){
        cday = (days < coupon.num_days) ? days : coupon.num_days;
        /* check qty */
        if(parseInt(qty) > parseInt(coupon.qty)){
          coupon_qty = coupon.qty;
          $('scoupon_qty').html(coupon.qty + " devices discount only.");
          }
        else{
          coupon_qty = qty;
          }
        coupon_with_promotion = coupon.with_promotions;
        
        coupon_discount = coupon_qty * (price_per_day - otherEarlyBirdDiscount - discount_device) * cday;

        /* Minus Early bird discount */
        if(coupon.with_promotions == 1 || coupon.with_promotions == 3 || coupon.with_promotions == 5 || coupon.with_promotions == 7){
          coupon_discount = coupon_discount * ( 1 - discount);
        }
        else{
          discount = 0;
        }
        /* END Minus Early bird discount */
        
        discount_amount = coupon_discount;
        coupon_code = coupon.coupon_num;
        coupon_desc = coupon.description;
        coupon_unlimited = coupon.is_unlimited;
      }

      if(coupon.amount != 0){
        
        coupon_with_promotion = coupon.with_promotions;
        

        /* Minus Early bird discount */
        if(coupon.with_promotions == 1 || coupon.with_promotions == 3 || coupon.with_promotions == 5 || coupon.with_promotions == 7){
          //coupon_discount = coupon_discount * ( 1 - discount);
        }
        else{
          discount = 0;
        }
        /* END Minus Early bird discount */
        coupon_discount = parseFloat(coupon.amount);
        
        discount_amount = coupon_discount;
        coupon_code = coupon.coupon_num;
        coupon_desc = coupon.description;
        coupon_unlimited = coupon.is_unlimited;
      }

      if(coupon.num_days == 0 && coupon.discount == 0 && coupon.amount == 0){
        discount_amount = 0;
        coupon_code = coupon.coupon_num;
        coupon_desc = coupon.description;
        coupon_unlimited = coupon.is_unlimited;
      }


    }


    /* More than 2 devices discount */
    if($('[name="chk_more_2_devices"]').prop('checked')){
      
      /* Check discount for 4 countries */
      if($('[name="country"]').val() == 'Japan' || $('[name="country"]').val() == 'Korea' || $('[name="country"]').val() == 'Taiwan' || $('[name="country"]').val() == 'Thailand'){
          /* Check discount condition*/
          if($('[name="qty"]').val() > 1){
            discount_device = 5;
            promo_desc = ' / 孖住上優惠';
          }
        }

    }

    /* Night promotion */
    if($('[name="chk_night_promo"]').prop('checked')){
      
        /* Check discount condition*/
        hour = $('[name="phour"]').val();
        minute = $('[name="pminute"]').val();

        if(parseInt(hour) >=19){
          if(days > 1){
            discount_night = 1; // one day discount
            promo_desc = promo_desc + ' / Night Promo';
          }
        }
    }

    
    var cost = price_per_day * days  * qty;
/**********************************************************/
 
    var c_discount = 0;
    if(coupon!=null) {
      c_days = coupon.num_days;
      c_discount = coupon.discount / 100;
    }
    else {c_days = 0;}

    var r_days = 0;
    r_days = (days <= c_days? 0: (days - c_days));
    
    
    /* coupon with two device promotion */
    if(coupon_with_promotion == 2 || coupon_with_promotion == 3 || coupon_with_promotion == 6 || coupon_with_promotion == 7){
      device_discount = (discount_device * qty * (r_days));
      //device_discount = (discount_device * qty * (days));
    }
    else{
      if(coupon != null){
        device_discount = 0;
      }
      else{
          device_discount = (discount_device * qty * (r_days));
      }
    }
    /* END coupon with two device promotion */
    
    night_discount = discount_night * qty * (getCostPerDay() - otherEarlyBirdDiscount - discount_device);
    night_discount = night_discount * (1 - (discount)); // 4 countries
    night_discount = night_discount * (1 - (c_discount)); //  - coupon discount
    discount_4countries = cost * discount;

    if(r_days == 0){
      device_discount = 0;
      night_discount = 0;
      discount_4countries = 0;
    }
    if(r_days == 1 && night_discount > 0){
      device_discount = 0;
    }

/**********************************************/
 
    /* Early bird for other countries */
    if(is_discountEarlybirdOther == true){
      if((coupon_with_promotion == 1 || coupon_with_promotion == 3 || coupon_with_promotion == 5 || coupon_with_promotion == 7)){
        discountEarlybirdOtherCountries =  days * qty * 10;
      }
      else{
        if(coupon != null){
          discountEarlybirdOtherCountries = 0;
        }
        else{
          discountEarlybirdOtherCountries =  days * qty * 10;
        }
      }
      
       if(night_discount > 0){
        //night_discount = night_discount - (qty * 10);
       }
    }

    /* coupon with night promotion */
    if(coupon_with_promotion == 4 || coupon_with_promotion == 5 || coupon_with_promotion == 6 || coupon_with_promotion == 7){ /* promotion is included */

      }
    else{ /* night promotion is not included */
      if(coupon != null){
        night_discount = 0;
      }
    }
    /* END coupon with night promotion */
    
    /*------------- Limited Promotion ------------------------ */
    if($('[name="chk_limited_promo"]').prop('checked')){
      
      var limited_countries = ["Macau" , "Singapore", "Malaysia", "Singapore+Malaysia", "Indonesia", "Vietnam", "Europe", "Philippine", "India", "Pakistan", "USA", "USA+Canada", "Canada", "Mexico", "Australia", "New Zealand", "Bangladesh", "Cambodia", "Qatar", "Russia", "Saudi Arabia", "South Africa", "Turkey", "UAE", "worldwide" ];
      var limited_except_plans = {"USA":"Verizon 4G LTE"};
      var limited_price =0;
      var sub_total = 0;
      var limited_price_per_day = 0;
      var limited_date = parseDate($('[name="departure_date"]').val());
      limited_month = limited_date.getMonth();

      var is_valid_plan = true;

      // check valid data plan with country
      if(limited_except_plans[$('[name="country"]').val()] != undefined){
        if($('[name="device_name"]').val() == limited_except_plans[$('[name="country"]').val()]){
          is_valid_plan = false;
        }
      }

      if(qty > 1 && (limited_countries.indexOf($('[name="country"]').val()) > -1) && (limited_month == 4 || limited_month == 5) && is_valid_plan){

        sub_total = cost - night_discount -  discountEarlybirdOtherCountries;
        limited_price_per_day = sub_total / (qty * (days - discount_night));
  
        limited_price = parseInt(qty / 2) * (days - discount_night) * 30 + Math.ceil(qty / 2) * limited_price_per_day * (days - discount_night); // odd + even
  
        // Calculate limited discount
        limited_discount = sub_total - limited_price;
        promo_desc = promo_desc + ' / Limited Offer';
      }
      
    }
    /***---------------End Limited Promotion------------------------***/
        
    discount_amount = coupon_discount + discount_4countries + ((promotion_discount /100) * cost) + device_discount + night_discount + discountEarlybirdOtherCountries + limited_discount;

    

    /* Agoda Coupon */
    if(agoda_booking != null){
  
      array_countries = ["Japan","Korea","China","Taiwan","Hong Kong", "Macau", "Thailand", "Vietnam", "Philippines","Cambodia","Singapore", "Malaysia", "Indonesia"];    
      array_countries2 = ["USA","Canada","Mexico","Australia","Russia", "UAE", "Europe"];
      european_countries = ["Albania","Andorra","Austria","Azerbaijan","Belarus","Belgium","Bosnia Herzegovina","Bulgaria","Croatia","Cyprus","Czech Republic","Denmark","Estonia","Faroe Islands","Finland","France","Georgia","Germany","Gibraltar","Greece","Greenland","Guernsey","Hungary","Iceland","Ireland","Isle Of Man","Italy","Jersey","Kosovo","Latvia","Liechtenstein","Lithuania","Luxembourg","Macedonia","Malta","Moldova","Monaco","Montenegro","Netherlands","Norway","Poland","Portugal","Romania","Russia","San Marino","Serbia","Slovakia","Slovenia","Spain","Sweden","Switzerland","Turkey","Ukraine","United Kingdom"];
        
      /* Check discount for 13 countries */
      agoda_country = agoda_booking.Bookings.Booking.Country;
      checkInDate = agoda_booking.Bookings.Booking.CheckInDate;
      checkOutDate = agoda_booking.Bookings.Booking.CheckOutDate;
      depDate = $('[name="departure_date"]').val();
      arrivalDate = $('[name="arrival_date"]').val();
      
      d_checkInDate = parseDate(checkInDate);
      d_checkOutDate = parseDate(checkOutDate);
      d_depDate = parseDate(depDate);
      d_arrivalDate = parseDate(arrivalDate);
      d_today = new Date();

      /* agoda country rename */
      if(agoda_country == 'South Korea'){
        agoda_country = 'Korea';
      }

      if(agoda_country == 'United States of America'){
        agoda_country = 'USA';
      }

      if(agoda_country == 'United Arab Emirates'){
        agoda_country = 'UAE';
      }

      if(european_countries.indexOf(agoda_country) > -1){
        agoda_country = 'Europe';
      }

      /* End agoda country rename */

      var country_name = "";
      if($('[name="country"]').val() != null){
        country_name = $('[name="country"]').val();
      }
      
      //if((country_name.indexOf(agoda_country) > -1) && (d_today < d_checkInDate)){
      if((country_name.indexOf(agoda_country) > -1) && (d_depDate <= d_checkInDate) && (d_checkInDate <= d_arrivalDate) && (d_arrivalDate >= d_checkOutDate) && (d_depDate <= d_checkOutDate)){
        /* 1st promotion areas */
        if(array_countries.indexOf(agoda_country) != -1){
            
          if(agoda_booking.Bookings.Booking.Status != "BookingCharged"){
            payment = 0;
          }
          else{
            payment = agoda_booking.Bookings.Booking.TotalRateUSD["@attributes"].inclusive * HKD_rate;
          }

          /* payment based amount 500 */
          if(payment > 500){
            int_pay = parseInt(payment / 500);
            //int_pay = int_pay * 500;
            //agoda_days = daydiff(d_checkInDate, d_checkOutDate);
            //int_pay = int_pay > agoda_days ? agoda_days : int_pay; 
            
            agoda_discount = int_pay * getCostPerDay();
          
            discount_amount = parseFloat(agoda_discount) + parseFloat(night_discount);
            
            if(discount_amount > cost) {
              agoda_discount = cost - night_discount;
              discount_amount = agoda_discount + night_discount;
            }
            else{
              
            }
            agoda_desc = " / Agoda";
          }
        }

        /*2nd promotion areas */
        if(array_countries2.indexOf(agoda_country) != -1){
          
          if(agoda_booking.Bookings.Booking.Status != "BookingCharged"){
            payment = 0;
          }
          else{
            payment = agoda_booking.Bookings.Booking.TotalRateUSD["@attributes"].inclusive * HKD_rate;
          }

          /* payment based amount 500 */
          if(payment > 1500){
            int_pay = parseInt(payment / 1000);
            //int_pay = int_pay * 500;
            //agoda_days = daydiff(d_checkInDate, d_checkOutDate);
            //int_pay = int_pay > agoda_days ? agoda_days : int_pay;
            
            agoda_discount = int_pay * getCostPerDay();
          
            discount_amount = parseFloat(agoda_discount) + parseFloat(night_discount);
            if(discount_amount > cost) {
              agoda_discount = cost - night_discount;
              discount_amount = agoda_discount + night_discount;
            }
            else{
              
            }
            agoda_desc = "Agoda";
          }
        }
        
      }
    }
    /* \End Agoda discount */
    

    var total_cost = cost + phone_cost + delivery_cost + deposit;
    total_cost = total_cost - discount_amount;

    // prevent minus
    if((total_cost - deposit) < 0){
      total_cost = deposit;
    }

    /* Additional devices */
    var powerbank = '&powerbank=' + qty;
    var usb_cable = '&usb_cable=' + (parseInt(qty) + parseInt(phone_qty));
    var adapter = '&adapter=' + (parseInt(qty) + parseInt(phone_qty));
    var pouch = '&pouch=' + (parseInt(qty) + parseInt(phone_qty));
    var event_coupon_code = '&event_coupon_code=' + coupon_code;
    var desc = coupon_desc + promo_desc + agoda_desc;

    if(desc.charAt(1) == '/'){
      desc = desc.substring(2);
    }
    
    var discount_desc = '&discount_desc=' + desc
    var c_unlimited_used = '&is_unlimited=' + coupon_unlimited;

    var language = "&language=<?php echo $Language->getLanguage() ?>";

    
    var data = "u=" + u + "&deposit=" + deposit + "&delivery_cost=" + delivery_cost + "&total_cost=" + total_cost.toFixed(1) + "&cost=" + cost.toFixed(1) + "&i_qty_chk=" + phone_qty + "&phone_cost=" + phone_cost + "&price_per_day=" + price_per_day + '&discount=' + discount_amount.toFixed(1) + powerbank + usb_cable + adapter + pouch + event_coupon_code + discount_desc + c_unlimited_used + language;

    $('#frmOrder').find('#pickuplocation').removeAttr('disabled');    
    $('#frmOrder').find('#return_location').removeAttr('disabled');
    
    data = data + "&" + $('#frmOrder').serialize();

    $('#saveloading').css('display','inline');
    $.ajax({
      type:'post',
      data: data,
      url: 'admin/modules/ajax/save_order.php',
      success:function(result){
          var rs = $.parseJSON(result);
          if(rs.success=='true'){
            /*Go to success page*/
            if($('#payment_type').val() == 'bank'){
              window.location.href = 'order_complete.php?c=' + $('[name="country"]').val();             
            }
            // Go to paydollar
            
            if($('#payment_type').val() == 'card'){
              $('[name=successUrl]').val("http://bananawifi.com/pay_card_success.php?c=" + $('[name="country"]').val());
              $('[name=failUrl]').val("http://bananawifi.com/order_complete.php?c=" + $('[name="country"]').val());
              $('[name=errorUrl]').val("http://bananawifi.com/order_complete.php?c=" + $('[name="country"]').val());
              $('[name=amount]').val(total_cost);
              $('[name=orderRef]').val(rs.order_number[0]['LAST_INSERT_ID()']);
              $('#payForm').submit();
            }         
          }
        }
    });
  }

  var fwdErrorCode = new Object();
  fwdErrorCode.Error_Code_301 = "<?=$Language->getText(Error_Code_301)?>";
  fwdErrorCode.Error_Code_302 = "<?=$Language->getText(Error_Code_302)?>";
  fwdErrorCode.Error_Code_303 = "<?=$Language->getText(Error_Code_303)?>";
  fwdErrorCode.Error_Code_310 = "<?=$Language->getText(Error_Code_310)?>";
  fwdErrorCode.Error_Code_311 = "<?=$Language->getText(Error_Code_311)?>";
  fwdErrorCode.Error_Code_312 = "<?=$Language->getText(Error_Code_312)?>";
  fwdErrorCode.Error_Code_313 = "<?=$Language->getText(Error_Code_313)?>";
  fwdErrorCode.Error_Code_314 = "<?=$Language->getText(Error_Code_314)?>";
  fwdErrorCode.Error_Code_315 = "<?=$Language->getText(Error_Code_315)?>";
  fwdErrorCode.Error_Code_316 = "<?=$Language->getText(Error_Code_316)?>";
  fwdErrorCode.Error_Code_317 = "<?=$Language->getText(Error_Code_317)?>";
  fwdErrorCode.Error_Code_318 = "<?=$Language->getText(Error_Code_318)?>";
  fwdErrorCode.Error_Code_319 = "<?=$Language->getText(Error_Code_319)?>";
  fwdErrorCode.Error_Code_320 = "<?=$Language->getText(Error_Code_320)?>";
  fwdErrorCode.Error_Code_321 = "<?=$Language->getText(Error_Code_321)?>";
  fwdErrorCode.Error_Code_323 = "<?=$Language->getText(Error_Code_323)?>";
  fwdErrorCode.Error_Code_600 = "<?=$Language->getText(Error_Code_600)?>";
  fwdErrorCode.Error_Code_601 = "<?=$Language->getText(Error_Code_601)?>";
  fwdErrorCode.Error_Code_602 = "<?=$Language->getText(Error_Code_602)?>";
  fwdErrorCode.Error_Code_603 = "<?=$Language->getText(Error_Code_603)?>";


  $('#btnSumitOrder').click(function(){

    if(!$('#frmOrder').valid()) return;
    if(!confirm('您想確認訂單嗎 ?\n  Do you want to submit your order form?')) return;

    $('#btnSumitOrder').attr('disabled','disabled');

    if( $('#checkbox_plan').is(":checked") ){

      if( !fwd_data ){
          alert(fwdErrorCode['Error_Code_301']);
          $('#btnSumitOrder').removeAttr('disabled');
          return;
      }

      if(fwd_data['departureDate']){
        var v = fwd_data['departureDate'];
        v = v.split('-');
        if( v.length == 3 ){
          fwd_data['departureDate'] = v[1] + '/' + v[2] + '/' + v[0];
        }
      } 
      if(fwd_data['returnDate']){
        var v = fwd_data['returnDate'];
        v = v.split('-');
        if( v.length == 3 ){
          fwd_data['returnDate'] = v[1] + '/' + v[2] + '/' + v[0];
        }
      }
      if(fwd_data['applicant']['dob']){
        var v = fwd_data['applicant']['dob'];
        v = v.split('-');
        if( v.length == 3 ){
          fwd_data['applicant']['dob'] = v[1] + '/' + v[2] + '/' + v[0];
        }
      }
      fwd_data['applicant']['contactNo'] = $('[name="phone"]').val();
      fwd_data['applicant']['email'] = $('[name="email"]').val();
      fwd_data['applicant']['hkid'] = fwd_data['applicant']['hkid'].replace("(","");
      fwd_data['applicant']['hkid'] = fwd_data['applicant']['hkid'].replace(")","");
      for( var i=0;i<Object.keys(fwd_data['insuredPersonList']).length;i++){
        fwd_data['insuredPersonList'][i]['hkid'] = fwd_data['insuredPersonList'][i]['hkid'].replace("(","");
        fwd_data['insuredPersonList'][i]['hkid'] = fwd_data['insuredPersonList'][i]['hkid'].replace(")","");
      }
      $.ajax({
                type: "POST",
                url: "http://uat.dreamover-studio.cn/bananawifi/rest/index.php/api/fwd_api/submit_application",
                data: fwd_data,
                dataType: 'json',
                cache: false,
                success: function(res){
                  if(res.status){
                    if(!res.result){
                      i--;
                      console.log("i", i);
                      // $(".btn-right").show();
                      $('#btnSumitOrder').removeAttr('disabled');
                      alert("提交失敗！請確認提供的是真實資料");
                      return;
                    } else if(res.result.errorCode){
                      alert(fwdErrorCode['Error_Code_'+res.result.errorCode]);
                      $('#btnSumitOrder').removeAttr('disabled');
                      return;
                    } else {
                      // 獲取 submit 之後得到的 ticket 和 tradeNo，用於只有 confirm
                      $("#insurance #insurance-policyTicket").val(res.result.policyTicket);
                      $("#insurance #insurance-merchantTradeNo").val(res.result.merchantTradeNo);
                      submitOrder();
                    return;
                    }
                  }
                },
                error: function(res){
                  $('#btnSumitOrder').removeAttr('disabled');
                  alert("連接服務器失敗！請稍後再試");
                  return;
                }
              });
    }else{
      submitOrder();
    }
  });

    function parseDate(str) {
        var mdy = str.split('-')
        return new Date(mdy[0],mdy[1]-1,mdy[2]);
    }

  function daydiff(first, second) {
    oneday = 1000 * 3600 * 24;
    return (Math.ceil((second.getTime() - first.getTime()) / oneday) + 1);
  }

  /* Form Validation */
  $('#frmOrder').validate({

    rules:{
      country: 'required',
      qty: 'required',
      departure_date: 'required',
      arrival_date: 'required',
      fullname: 'required',
      email: {email: true,required: true},
      phone: {number: true,required: true},
      pickuplocation: 'required', 
      shop_detail: 'required',
      city: 'required',
      returnlocation: 'required',
      payment_type: 'required',
      agree: 'required',
      flight_no: 'required',
      bank_account: 'required',
      delivery_address: 'required',
      card_number: {number: true,required: true},
      card_expire_month: {number: true,required: true,maxlength: 2, minlength:2},
      card_expire_year: {number: true,required: true,maxlength: 4, minlength:4},
      card_security_code: {number: true,required: true,maxlength: 3, minlength:3},
      card_type: 'required',
      device_name: 'required',
      
    },
    errorPlacement: function(error,element){
              $(element).addClass("error");
              if(element[0].name == 'agree'){
                $("#agreewrap").attr("style","border: 1px solid red;margin-top: -5px;");
              }
              if(element[0].name == 'card_type'){
                $("#card_type_required").html("Please select Credit Card.");
              }
              return false;
            }
    });


  $('#country li a').click(function(event){
    event.preventDefault();
    $('#short-order').html('');
    var s_top = $(this).offset().top;
    var s_left = parseInt($('#country').offset().left) + 158;
    var country_en = $(this).children('.country_en').val();
    var country_ch = $(this).children('.country_ch').html();
    var bgimage = $(this).children('.bgimage').val();
    backgroundImage(bgimage);
    //var data = 'country=' + country_en + '&lang_ch=' + country_ch;
    var data = {'country': country_en,'lang_ch': country_ch};
    $.ajax({
        url: 'ajax/short_order.php?',
        data: data,
        type: 'post',
        contentType: "application/x-www-form-urlencoded; charset=iso-8859-1",
        success: function(data){
          $('#short-order').html(data);
        }
      });
    
    var top = s_top -50;
    $('#short-order').css({'top': top + 'px'});
    $('#short-order').css({'left':s_left + 'px'});

    var panel = $(this);
    $(window).resize(function(){
      var s_top = panel.offset().top;
      var s_left = parseInt($('#country').offset().left) + 158;
      top = s_top -50;
      $('#short-order').css({'top': top + 'px'});
      $('#short-order').css({'left':s_left + 'px'});
    });
    
  });

  function backgroundImage(country){
    
    country = country.replace('+','_');
    $('#main_vigual').attr("style" , "height: 590px;transition:background-image 1s ease; background:url('../images/sight/" + country + "') no-repeat center 100% !important;background-size: auto 100%;");
  }
  

   $('body').click(function(e){
      if(e.target.className != 's_country' && e.target.className != 'country_ch'){
        if (!$(e.target).closest('#short-order').length) {
          $('#short-order').css('display','none');    
          } 
      }
      else{
        $('#short-order').css('display','inline');
      }
    });


    <?php 
      if($is_promotion){
        echo "$('#txt_departure_date').datepicker('setDate','$promotion_date');";
        echo "$('#txt_departure_date').datepicker('setStartDate','$promotion_date');";
        echo "$('#txt_departure_date').datepicker('setEndDate','$promotion_date');";
      }
    ?>

    function daysInMonth(month,year) {
        return new Date(year, month, 0).getDate();
    }

    $('.pop_info').click(function(e){
    event.preventDefault();
    img = $(this).attr('href');
    BootstrapDialog.show({
      message: '<div><center><img src="images/popup/' + img + '" /></center></div>'
    });
    });

    $('[name="agree"]').change(function(){
      if(!$('[name="agree"]').prop('checked')) return;
    
    if(parseInt($('[name="phour"]').val()) >= 19){
      alert('<?php echo $Language->getText(IF_YOUR_PICKUP_TIME); ?>');
    }
    
    });

    function isShopClosed(close_date){
      var stop_from_1 = parseDate("2017-04-11");
    var stop_to_1 = parseDate("2017-04-19");
    var stop_from_2 = parseDate("2017-04-26");
    var stop_to_2 = parseDate("2017-05-09");
    var arv_date = parseDate(close_date);
    var shop_return_close = false;
    if((stop_from_1 <= arv_date && arv_date <=stop_to_1) || (stop_from_2 <= arv_date && arv_date <=stop_to_2)){
      shop_return_close = true;
    }
    return shop_return_close;
    }

    function get_insurance(){
    if( $('#checkbox_plan').is(":checked") ){
      var name = '<?=$coupon['name']?>';
      var num_days = <?=$coupon['num_days']?>;
      var qty = <?=$coupon['qty']?>;
      var product = '<?=implode(',', $coupon['product'])?>';
      var with_promotions = '<?=$coupon['with_promotions']?>';
      var country = '<?=implode(',', $coupon['country'])?>';

      var individual_price = parseFloat($('[name="individual_price"]').val());
      var family1_price = parseFloat($('[name="family1_price"]').val());
      var family2_price = parseFloat($('[name="family2_price"]').val());
      var discount = parseFloat($('[name="discount_price"]').val());
      var individual = parseInt($('[name="individual"]').val());

      var disable = <?=$coupon['disable']?>;
      if( country ){
        country = country.split(',');
      }
      if( with_promotions ){
        with_promotions = parseInt(with_promotions);
      }

      var insurance_total = $('.insurance-discount-price').html();
      var total_days = -1;
      var departureDate = $('[name="departureDate"]').val();
      var returnDate = $('[name="returnDate"]').val();
      if( departureDate && returnDate ){
        total_days = daydiff(parseDate(departureDate), parseDate(returnDate));
      }

      var is_promotion = true;

      var early_promo_code = [1,3,5,7];
      var twin_promo_code  = [2,3,6,7];
      var night_promo_code = [4,5,6,7];
      if( $('[name="chk_discount"]').is(":checked") && early_promo_code.indexOf(parseInt(with_promotions)) < 0 ){
        is_promotion = false;
      }
      if( $('[name="chk_more_2_devices"]').is(":checked") && twin_promo_code.indexOf(parseInt(with_promotions)) < 0 ){
        is_promotion = false;
      }
      if( $('[name="chk_night_promo"]').is(":checked") && night_promo_code.indexOf(parseInt(with_promotions)) < 0 ){
        is_promotion = false;
      }

      if( is_promotion &&
        (disable == 0 || ( disable == 1 && $('#scoupon_discount').html() == '' )) &&
        qty <= parseInt($('[name="qty"]').val()) &&
        num_days <= parseInt(total_days) &&
        $.inArray($('[name="country"]').val(), country) != -1
      ){
        $("#banana_care_name").html(name);
        if( $('#insurance #insurance-subPlan').val() == 'individual' ){
          var total = individual_price*individual-discount;
          if( total < 0 ){
            total = 0;
          }
          $("#insurance-total").val(total);
        }else if( $('#insurance #insurance-subPlan').val() == 'family' ){
          if( parseInt($('[name="parent"]').val()) == 1 ){ 
            var total = family1_price+individual_price*parseInt($('[name="other"]').val())-discount;
            if( total < 0 ){
              total = 0;
            }
            $("#insurance-total").val(total);
          }else if( parseInt($('[name="parent"]').val()) == 2 ){
            var total = family2_price+individual_price*parseInt($('[name="other"]').val())-discount;
            if( total < 0 ){
              total = 0;
            }
            $("#insurance-total").val(total);
          }
        }
      }else{
        $("#banana_care_name").html('');
        $("#insurance-total").val(insurance_total);
      }
      $("#banana_care").html($("#insurance-total").val());
      $(".insurance-discount-price").html($("#insurance-total").val());
      $("#insurance #insurance-totalDue").html($("#insurance-total").val());
      ordersummary();
    }else{
      $("#banana_care_name").html('');
      $("#insurance-total").val(0);
      $("#banana_care").html($("#insurance-total").val());
      $("#insurance #insurance-totalDue").html($("#insurance-total").val());
      ordersummary();
    }
    // $('#realtime-price').html('即時報價');
    // $('#origin-price').hide();
    }

    function get_fwd_price(){
      var $inputs = $('#insurance-section1 :input');
    // not sure if you wanted this, but I thought I'd add it.
    // get an associative array of just the values.
    var data = {};
    $inputs.each(function() {
      if(this.name){
        if( this.name == 'departureDate' || this.name == 'returnDate' ){
          var v = $(this).val();
          if( v ){
            v = v.split('-');
            v = parseInt(v[1])+'/'+v[2]+'/'+v[0];
            data[this.name] = v;
          }
        }else{
          data[this.name] = $(this).val();
        }
      }
    });
    if( (!data.departureDate || !data.returnDate) && ( $('[name="departure_date"]').val() !== '' && $('[name="arrival_date"]').val() !== '' ) ){
      var v = $('[name="departure_date"]').val();
      v = v.split('-');
      v = parseInt(v[1])+'/'+v[2]+'/'+v[0];
      data.departureDate = v;
      //
      var v = $('[name="arrival_date"]').val();
      v = v.split('-');
      v = parseInt(v[1])+'/'+v[2]+'/'+v[0];
      data.returnDate = v;
      //
      data.total_days = daydiff(parseDate($('[name="departure_date"]').val()), parseDate($('[name="arrival_date"]').val()));
    }else{
      data.total_days = daydiff(parseDate($('[name="departureDate"]').val()), parseDate($('[name="returnDate"]').val()));
    }
    if( data.departureDate && data.returnDate ){
      $.ajax({
          type: "POST",
          url: "admin/modules/ajax/get_fwd_price.php",
          data: data,
          dataType: 'json',
          cache: false,
          success: function(res){
              if(res.status){
                var result = res.result;
                if(result){
                $("#insurance .btn-right").removeClass("hide");
              var individual = result[0];
              var family1 = result[1];
              var family2 = result[2];
              $("#insurance-show-sum").removeClass("hide");
              $("#insurance-show-sum-loading").addClass("hide");
              $(".insurance-original-price").each(function(){
                if( data.subPlan == 'individual' ){
                  $( this ).html(individual.origin_price);
                }else if( data.subPlan == 'family' ){
                  if( data.parent == 1 ){
                    $( this ).html(family1.origin_price);
                  }else if( data.parent == 2 ){
                    $( this ).html(family2.origin_price);
                  }
                }
              })
              var total_price = 0;
              var discount_price = 0;
              $(".insurance-discount-price").each(function(){
                total_price = 0;
                if( data.subPlan == 'individual' ){
                  var totalDue = parseInt(individual.special_price) * parseInt(data.individual);
                  var discount = parseInt(individual.discount_price) * parseInt($('[name="qty"]').val());
                  total_price = totalDue;
                  discount_price = discount;
                  if( total_price < 0 ){
                    total_price = 0;
                  }
                }else if( data.subPlan == 'family' ){
                  if( data.parent == 1 ){
                    var totalDue = parseInt(family1.special_price) + parseInt(individual.special_price) * parseInt(data.other);
                    var discount = parseInt(family1.discount_price) * parseInt($('[name="qty"]').val());
                    total_price = totalDue;
                    discount_price = discount;
                    if( total_price < 0 ){
                      total_price = 0;
                    }
                  }else if( data.parent == 2 ){
                    var totalDue = parseInt(family2.special_price) + parseInt(individual.special_price) * parseInt(data.other);
                    var discount = parseInt(family2.discount_price) * parseInt($('[name="qty"]').val());
                    total_price = totalDue;
                    discount_price = discount;
                    if( total_price < 0 ){
                      total_price = 0;
                    }
                  }
                }
                $( this ).html(total_price);
                $("#insurance-total").val($( this ).html());
                $("#banana_care").html($("#insurance-total").val());
              });
              $('[name="individual_price"]').val(individual.special_price);
              $('[name="family1_price"]').val(family1.special_price);
              $('[name="family2_price"]').val(family2.special_price);
              $('[name="discount_price"]').val(discount_price);
              if( $('#checkbox_plan').is(":checked") ){
                $('#realtime-price').html('<?php echo $Language->getText(Instant_quotation); ?>');
                $('#origin-price').hide();
              }
              get_insurance();
                }
            }
          },
          error: function(res){
            $("#insurance .btn-right").addClass("hide");
          }
      });
    }
    }

  $("#checkbox_plan").click(function(){
    var departure_date = $('[name="departure_date"]').val();
    var arrival_date = $('[name="arrival_date"]').val();
    if( departure_date && arrival_date ){
      // departure_date = departure_date.split('-');
      // arrival_date = arrival_date.split('-');
      // $('[name="departureDate"]').val(departure_date[1]+'/'+departure_date[2]+'/'+departure_date[0]);
      // $('[name="returnDate"]').val(arrival_date[1]+'/'+arrival_date[2]+'/'+arrival_date[0]);
      $('[name="departureDate"]').datepicker('setDate', parseDate(departure_date));
      $('[name="returnDate"]').datepicker('setDate', parseDate(arrival_date));
      $('.btn-right').removeClass('hide');
    }
    get_fwd_price();
  });

  $(".btn_type").click(function(){
    get_fwd_price();
  });

  $('[name="departureDate"], [name="returnDate"]').change(function(){
    get_fwd_price();
  });

  $(".insurance-discount-price").change(function(){
    get_fwd_price();
  });

  $('[name="departureDate"]').on('changeDate',function(e){
    ddate = $('[name="departureDate"]').datepicker('getDate');
    d = new Date(e.date);
    d.setDate(d.getDate() + 1);
    
    $('[name="returnDate"]').datepicker('setStartDate',d);
  });

  $('[name="returnDate"]').on('changeDate',function(e){
    ddate = $('[name="returnDate"]').datepicker('getDate');
    d = new Date(e.date);
    d.setDate(d.getDate() - 1);
    
    $('[name="departureDate"]').datepicker('setEndDate',d);
  });

  $('.applicant_dob').on('changeDate',function(e){
    var ddate = $('[name="returnDate"]').datepicker('getDate');
    var start = new Date(e.date);
    var end = new Date();
    var days = Math.floor((end.getTime() - start.getTime())/(24*3600*1000));
    var years = days/365;
    if( years < 18 ){
      $('.applicant_ageRangeCode').val(1);
    }else if( 18 <= years && years < 71 ){
      $('.applicant_ageRangeCode').val(2);
    }else if( 71 <= years && years < 85  ){
      $('.applicant_ageRangeCode').val(3);
    }
  });

  // fwd.js content start
$('#insurance #departureDate').datepicker({autoclose: true,disabled: true, todayHighlight: true, format: 'yyyy-mm-dd',startDate: 'today'});
$('#insurance #returnDate').datepicker({autoclose: true,disabled: true, todayHighlight: true, format: 'yyyy-mm-dd', startDate: 'today'});
$("#insurance .applicant_dob").datepicker({autoclose: true,disabled: false, todayHighlight: false, format: 'yyyy-mm-dd', startDate: '1900-01-01', endDate: 'today', startView: 2});
var pass = [false,false,false];
/*
 * 檢查 #insurance-section1 裡面所有的更新，然後實時獲取報價
 */
$('#insurance-section1 :input').change(function(){
  $("#insurance-show-sum-loading").removeClass("hide");
  var $inputs = $('#insurance-section1 :input');

  // not sure if you wanted this, but I thought I'd add it.
  // get an associative array of just the values.
  var data = {};
  $inputs.each(function() {
  if(this.name){
    if( this.name == 'departureDate' || this.name == 'returnDate' ){
      var v = $(this).val();
      if( v ){
        v = v.split('-');
        v = parseInt(v[1])+'/'+v[2]+'/'+v[0];
        data[this.name] = v;
      }
    }else{
      data[this.name] = $(this).val();
    }
  }
  });
  $.ajax({
    type: "POST",
    url: "http://uat.dreamover-studio.cn/bananawifi/rest/index.php/api/fwd_api/get_quote",
    data: data,
    dataType: 'json',
    cache: false,
    success: function(res){
      pass[0] = false;
        if(res.status){
          var result = res.result;
          if(!result){
            $("#insurance .btn-right").addClass("hide");
              alert("<?php echo $Language->getText(Error_Code_888); ?>");
          } else if(result.errorCode){
        $("#insurance .btn-right").addClass("hide");
        $("#insurance-show-sum").addClass("hide");
        $("#insurance-show-sum-loading").addClass("hide");
          } else {
            if( result.plans ){
              pass[0] = true;
            // $("#insurance .btn-right").removeClass("hide");
          // var plan = result.plans[0];
          // $("#insurance-show-sum").removeClass("hide");
          // $("#insurance-show-sum-loading").addClass("hide");
          // $(".insurance-original-price").each(function(){
          //  $( this ).html(plan.totalDue);
          // })
          // $(".insurance-discount-price").each(function(){
          //  $( this ).html(plan.totalDue-plan.discount);
          //  $("#insurance-total").val($( this ).html());
          //  $("#banana_care").html($("#insurance-total").val());
          // });
          // $('#realtime-price').html('<?php echo $Language->getText(Instant_quotation); ?>');
          // $('#origin-price').hide();
          // get_fwd_price();
          get_fwd_price();
            }
          }
      }
    },
    error: function(res){
      pass[0] = true;
      $("#insurance .btn-right").addClass("hide");
    }
  });
})

/*
 * 檢查 #insurance-section1 裡面 select 的更新，然後在 slide2 生成相應的資料表格
 */
$('#insurance-section1 select').change(function(){
  // Loop for creating dom
  var currentSubPlan = $("#insurance #insurance-subPlan").val();
  var temp = parseInt($(this).val());
  var base = 0;
  var label = '<?php echo $Language->getText(Child); ?>';
  var type = $(this).attr("name");
  var relationshipCode = "SE";
  $("#insurance-section2 #insurance-plan-"+currentSubPlan+" #insurance-plan-"+currentSubPlan+"-"+type).html('');
  var insuredPersonList = $("#insurance-plan-"+currentSubPlan+" .family_number").length - 1;
  switch(type){
    case 'individual':
      base = 1;
      label = '<?php echo $Language->getText(Applicant); ?>';
      relationshipCode = "RF";
      break;
    case 'parent':
      base = 1;
      label = '<?php echo $Language->getText(Parent); ?>';
      relationshipCode = "SP";
      break;
    case 'other':
      label = '<?php echo $Language->getText(Other_passengers); ?>';
      relationshipCode = "OT";
      break;
    case 'child':
      label = '<?php echo $Language->getText(Child); ?>';
      relationshipCode = "CH";
      break;
  }
  if(temp > base){
    for(var i = base; i < temp; i++){
      var tempHtml = '<div class="family_number">' 
                  + '<div>'+label+(i+1)+'</div>'
                  + '<input type="text" placeholder="<?php echo $Language->getText(Name2); ?>" class="form-control input-sm" name="insuredPersonList['+insuredPersonList+'][name]">'
                  + '<select class="form-control input-sm"  name="insuredPersonList['+insuredPersonList+'][id_type]">'
                  + '<option value="hkid" checked>HKID</option>'
                  + '<option value="passport" checked>Passport</option>'
                  + '</select>'
                  + '<input type="text" value="" class="form-control input-sm" name="insuredPersonList['+insuredPersonList+'][id_number]" placeholder="<?php echo $Language->getText(HKID); ?>" style="max-width: 160px;">'
                  + '<select class="form-control input-sm" name="insuredPersonList['+insuredPersonList+'][ageRangeCode]" style="max-width: 120px;">'
                  + '<option value="" checked><?php echo $Language->getText(Age_range); ?></option>'
                  + '<option value="1"><?php echo $Language->getText(Age1); ?></option>'
                  + '<option value="2"><?php echo $Language->getText(Age2); ?></option>'
                  + '<option value="3"><?php echo $Language->getText(Age3); ?></option>'
                  + '</select>'
                  + '<input type="hidden" name="insuredPersonList['+insuredPersonList+'][relationshipCode]" value="'+relationshipCode+'" />'
                  + '<input type="hidden" name="insuredPersonList['+insuredPersonList+'][beneficiaryRelationshipCode]" value="SE" />'
                  + '</div>';
      $("#insurance-section2 #insurance-plan-"+currentSubPlan+" #insurance-plan-"+currentSubPlan+"-"+type).append(tempHtml);
      insuredPersonList++;
    }
  }
});
// $(document).ready(function(){
var fwd_data = null;
  $(".range .toggle").click(function(){
    $(".toggle-content").slideToggle("slow");
    $(this).toggleClass("glyphicon-minus");
  })
  $(".select-group .bg-orange").click(function(event) {
    $(this).siblings(".type").slideToggle(400).parent("li").siblings("li").children(".type").slideUp(400);
  });

  $(".btn_type").click(function(event) {
    $(this).addClass("active").siblings(".btn_type").removeClass("active");
  });

  $("#checkbox_plan").click(function(event) {
    if ($(this).prop("checked")) {
      $(".all-choose").show(); 
      $(".choose li").hide(500);
      $(".choose .choose1").show(500);
      
      var W = $(".all-choose").width();
      var count = $(".all-choose .choose li").length;
      var i = 0;

      /*向左按钮*/
      $(".btn-left").click(function(event) {
        // if(i == 1){
        //   $(".choose li").eq(0).show(500).siblings('li').hide(500);
        // }
        i--;
        move('previous');
        
      });

      /*向右按钮*/
      $(".btn-right").click(function(event) {
        // if($(".btn_type").eq(0)&&i == 0){
        //   $(".choose .choose2").show(500).siblings('li').hide(500);
        // }
        // if($(".btn_type").eq(1)&&i == 0){
        //   $(".choose .choose3").show(500).siblings('li').hide(500);
        // }

        i++;
        move('next');

      });
      /*移动事件*/
      function move(direction) {
        /*
         * 初始化函數
         */
        var allfilled = true;
        var currentSubPlan = $("#insurance #insurance-subPlan").val();
        if(currentSubPlan == 'family'){
          $("#insurance-section2 #insurance-plan-family").removeClass("hide");
          $("#insurance-section2 #insurance-plan-individual").addClass("hide");
        } else {
          $("#insurance-section2 #insurance-plan-family").addClass("hide");
          $("#insurance-section2 #insurance-plan-individual").removeClass("hide");
        }
        $(".btn-left").show();

        /*
         * 進入最後一頁(slide3)之前要做的動作
         */
        if (i == count-1) {
            // 把 insurance-section2 裡面所有的 input 數據變成 javascript 的 object: data
            var currentSubPlan = $("#insurance #insurance-subPlan").val();
            var $inputs = $('#insurance-section2 #insurance-plan-'+currentSubPlan+' :input');
            var data = {};
            $inputs.each(function() {
              if($(this).val() === undefined || $(this).val().length == 0){
                i--;
                alert("請填妥所有資料");
                allfilled = false;
                return false;
                $(".choose .choose3").hide();
              }

              if(this.name.indexOf("[") != -1) {
                var temp = this.name;
                var tempAry = [];
                while(temp.indexOf("[") != -1) {
                  var tempName = temp.substr(0, temp.indexOf("["));
                  if(temp.indexOf("[")+1 <= temp.indexOf("]")){
                    temp = temp.substr(temp.indexOf("[")+1, temp.length);
                    temp = temp.substr(0, temp.indexOf("]"))+temp.substr(temp.indexOf("]")+1, temp.length);
                  }
                  tempAry.push(tempName);
                }
                tempAry.push(temp);
                switch(tempAry.length){
                  case 2:
                    if(data[tempAry[0]] === undefined) data[tempAry[0]] = {};
                    data[tempAry[0]][tempAry[1]] = $(this).val();
                    break;
                  case 3:
                    if(data[tempAry[0]] === undefined) data[tempAry[0]] = {};
                    if(data[tempAry[0]][tempAry[1]] === undefined) data[tempAry[0]][tempAry[1]] = {};
                    data[tempAry[0]][tempAry[1]][tempAry[2]] = $(this).val();
                    break;
                }
              } else {
                data[this.name] = $(this).val();
              }

              if(this.name == "directOptOut") data['directOptOut'] = $(this).prop("checked");
              if(this.name == "thirdPartyOptOut") data['thirdPartyOptOut'] = $(this).prop("checked");
            });

            // 檢查是否全部資料已填妥
            if(!allfilled) {
              return;
            } else {
              // 獲取section 1的data
              var $inputs = $('#insurance-section1 :input');
              $inputs.each(function() {
                  if(this.name && data[this.name] === undefined) data[this.name] = $(this).val();
              });
              // Clear all unnecessary item，刪除沒有用的數據
              delete data["child"];
              delete data["individual"];
              delete data["other"];
              delete data["parent"];

              var temp = 0;
              if(data["insuredPersonList"] !== undefined) temp = Object.keys(data["insuredPersonList"]).length;
              else data["insuredPersonList"]={};
              // Process the id type
              if(temp > 0){
                for(var j=temp-1; j >= 0; j--){
                  data["insuredPersonList"][j][data["insuredPersonList"][j]["id_type"]] = data["insuredPersonList"][j]["id_number"];
                  delete data["insuredPersonList"][j]["id_number"];
                  delete data["insuredPersonList"][j]["id_type"];
                  data["insuredPersonList"][j+1] = data["insuredPersonList"][j];
                }
              }
              var tempObj = {
                name: data["applicant"]["name"],
                hkid: data["applicant"]["hkid"],
                ageRangeCode: data["applicant"]["ageRangeCode"],
                relationshipCode: "SE",
                beneficiaryRelationshipCode: "SE"
              }
              data["insuredPersonList"][0] = tempObj;
              delete data["applicant"]["ageRangeCode"];

              // if(!data['thirdPartyOptOut'] || !data['directOptOut']){
              if(!data['thirdPartyOptOut']){
                i--;
                alert("必須同意條款才能進行下一步");
                allfilled = false;
                return false;
              }

              // Submit to server：所有數據通過檢查之後，提交到服務器
              $(".btn-right").hide();
              fwd_data = data;
              // $.ajax({
              //   type: "POST",
              //   url: "http://uat.dreamover-studio.cn/bananawifi/rest/index.php/api/fwd_api/submit_application",
              //   data: data,
              //   dataType: 'json',
              //   cache: false,
              //   success: function(res){
              //     if(res.status){
              //       if(!res.result){
              //         i--;
              //         console.log("i", i);
              //         $(".btn-right").show();
              //         alert("提交失敗！請確認提供的是真實資料");
              //         return;
              //       } else if(res.result.errorCode){
              //         switch(parseInt(res.result.errorCode)){
              //           case 301:
              //             break;
              //           default:
              //             alert("提交失敗！請檢查資料是否有誤");
              //             break;
              //         }
              //         $(".btn-right").show();
              //         i--;
              //         return;
              //       } else {
                      $(".choose .choose"+(i+1)).show(500).siblings('li').hide(500);
                      // 獲取 submit 之後得到的 ticket 和 tradeNo，用於只有 confirm
                      // $("#insurance #insurance-policyTicket").val(res.result.policyTicket);
                      // $("#insurance #insurance-merchantTradeNo").val(res.result.merchantTradeNo);

                      // 賦值最後一個slide的summary
                      $("#insurance #insurance-departureDate").html(data['departureDate']);
                      $("#insurance #insurance-returnDate").html(data['returnDate']);

                      var departureDate = data['departureDate'];
            var returnDate = data['returnDate'];
            var total_days = daydiff(parseDate(departureDate), parseDate(returnDate));
                      $("#insurance #insurance-totalDay").html(total_days);
                      $("#insurance #insurance-totalInsuredPerson").html(Object.keys(data["insuredPersonList"]).length);
                      // $("#insurance #insurance-totalDue").html(res.result.totalDue);

                      //insuredPersonTable 建立受保人列表表格
                      var temp = Object.keys(data["insuredPersonList"]).length;
                      $("#insurance #insurance-insuredPersonTable").html("");
                      var tempIndividual = $("#insurance #insurance-section1 select[name='individual']").val();
                      var tempParent = $("#insurance #insurance-section1 select[name='parent']").val();
                      var tempChild = $("#insurance #insurance-section1 select[name='child']").val();
                      var tempOther = $("#insurance #insurance-section1 select[name='other']").val();
                      if(currentSubPlan == 'family'){
                        tempIndividual = 0;
                      } else {
                        tempParent = tempChild = tempOther = 0;
                      }

                      for(var j = 0; j < temp; j++){
                        var tempHtml = '<tr>';
                        var temp2Individual = 0;
                        var temp2Parent = 0;
                        var temp2Child = 0;
                        var temp2Other = 0;
                        if(tempIndividual > 0){
                          temp2Individual++;
                          // tempHtml += '<td><?php echo $Language->getText(Individual); ?>'+temp2Individual+'</td>';
                          tempHtml += '<td><?php echo $Language->getText(Individual); ?></td>';
                          tempIndividual--;
                        } else if(tempParent > 0){
                          temp2Parent++;
                          // tempHtml += '<td><?php echo $Language->getText(Parent); ?>'+temp2Parent+'</td>';
                          tempHtml += '<td><?php echo $Language->getText(Parent); ?></td>';
                          tempParent--;
                        } else if(tempChild > 0){
                          temp2Child++;
                          // tempHtml += '<td><?php echo $Language->getText(Child); ?>'+temp2Child+'</td>';
                          tempHtml += '<td><?php echo $Language->getText(Child); ?></td>';
                          tempChild--;
                        } else if(tempOther > 0){
                          temp2Other++;
                          // tempHtml += '<td><?php echo $Language->getText(Other_passengers); ?>'+temp2Other+'</td>';
                          tempHtml += '<td><?php echo $Language->getText(Other_passengers); ?></td>';
                          tempOther--;
                        }
                        tempHtml += '<td>'+data["insuredPersonList"][j]['name']+'</td>';
                        tempHtml += '<td>'+ageGroup[data["insuredPersonList"][j]['ageRangeCode']-1]+'</td>';
                        tempHtml += '<td>'+ (data["insuredPersonList"][j]['hkid'] ? data["insuredPersonList"][j]['hkid'] : data["insuredPersonList"][j]['passport']) +'</td>';
                        tempHtml += '</tr>';
                        $("#insurance #insurance-insuredPersonTable").append(tempHtml);
                      }
                      $(".btn-right").hide();
                      // $(".btn-left").hide();
                      
                      $(".all-choose .choose").stop().animate({ marginLeft: 0 }, 500);
              // return;
              //       }
              //     }
              //   },
              //   error: function(res){
              //     alert("連接服務器失敗！請稍後再試");
              //     $(".btn-right").show();
              //     i--;
              //     return;
              //   }
              // });
              return;
            }
            
          }

          /*
           * 進入最後一頁(slide3)之前要做的動作
           */
          if (i == 0) {
              $(".choose .choose"+(i+1)).show(500).siblings('li').hide(500);
              $(".all-choose .choose").stop().animate({ marginLeft: 0 }, 500);
              $(".btn-left").hide();
              if(pass[0]) $(".btn-right").show();
              // $(".btn-right").click(function(event) {
              //   $("#insurance-section2").show(500).siblings('li').hide(500);
              // });
          }

          /*
           * 進入第二頁(slide2)之前要做的動作
           */
          if(i == 1){
            $(".btn-right").show();
            $('.first-person-name').val($('[name="fullname"]').val());
            $('.first-person-phone').val($('[name="phone"]').val());
            $('.first-person-email').val($('[name="email"]').val());
            // 顯示現在有幾多旅客，家長，子女，其他旅客
            $("#insurance-section2 .insurance-plan-family-parent-no").each(function(){
              $(this).html($("#insurance-section1 select[name='parent']").val());
            });
            $("#insurance-section2 .insurance-plan-family-other-no").each(function(){
              $(this).html($("#insurance-section1 select[name='other']").val())
            });
            $("#insurance-section2 .insurance-plan-family-child-no").each(function(){
              $(this).html($("#insurance-section1 select[name='child']").val())
            });
            $("#insurance-section2 .insurance-plan-individual-no").each(function(){
              $(this).html($("#insurance-section1 select[name='individual']").val())
            });
            $(".all-choose .choose").stop().animate({ marginLeft: 0 }, 500);
            $(".choose .choose"+(i+1)).show(500).siblings('li').hide(500);
            // $(".btn-left").click(function(event) {
            //     $(".choose li").eq(0).show(500).siblings('li').hide(500);
            // });

          }
          
      }

    } else {
        $(".all-choose").hide(); 
    }
  });
// });
  // fwd.js content end
});

</script>
</html>
