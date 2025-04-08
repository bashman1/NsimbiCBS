<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');
require_once('includes/functions.php');

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}

$response = new Response();

$title = 'REGISTER INDIVIDUAL';

if (isset($_POST['submit'])) {
    // $actype = isset($_POST['actype']) ? $_POST['actype'] : '';

    // if ($POST['is_client'] == 0) {
    // $passport_photo_name = $_POST['captured_image_data_2'];
    // } 

    


    // send these as empty strings
    $_POST['otherattach'] = null;
    $_POST['passport'] = null;
    $_POST['signature'] = null;
    $_POST['fing'] = null;


    $_POST['client_type'] = 'individual';




    $res = $response->addClient($_POST);



    // var_dump($res);
    // exit;

    if ($res['success']) {
        setSessionMessage(true, 'Client Created Successfully');
        Redirect('individual_clients_attachments.php?id=' . $res['message']);
    } else {
        setSessionMessage(false, 'Something went wrong! Check the Client\'s table to confirm if all the client\'s were created right.');
        RedirectCurrent();
        // header('Location:add_individual_client');
    }

    exit();
}

require_once('includes/head_tag.php');

$actypes = $response->getAllSavingsAccounts($user[0]['bankId'], $user[0]['branchId']);
?>


<body>

    <!--*******************
        Preloader start
    ********************-->
    <?php include('includes/preloader.php'); ?>
    <!--*******************
        Preloader end
    ********************-->


    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <?php
        include('includes/nav_bar.php');
        include('includes/side_bar.php');
        ?>
        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <div class="container-fluid">

                <!-- row -->
                <div class="row">
                    <div class="col-xl-12 col-xxl-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Add New Client
                                </h4>
                                <?php
                                if (isset($_GET['success#wizard_confirm'])) {
                                    echo '<script type="text/javascript">
                    mySuccess();
                   </script>';
                                    // unset($_SESSION['success']);
                                }
                                if (isset($_GET['error#wizard_confirm'])) {
                                    echo '<script type="text/javascript">
                                    myError();
                                   </script>';
                                }

                                ?>
                            </div>
                            <div class="card-body">

                                <div id="smartwizard" class="form-wizard order-create">
                                    <ul class="nav nav-wizard">
                                        <li><a class="nav-link" href="#wizard_Service">
                                                <span>1</span>
                                            </a></li>
                                        <li><a class="nav-link" href="#wizard_Time">
                                                <span>2</span>
                                            </a></li>
                                        <li><a class="nav-link" href="#wizard_Details">
                                                <span>3</span>
                                            </a></li>
                                        <li><a class="nav-link" href="#wizard_occupation">
                                                <span>4</span>
                                            </a></li>
                                        <li><a class="nav-link" href="#wizard_disability">
                                                <span>5</span>
                                            </a></li>

                                        <li><a class="nav-link" href="#wizard_confirm">
                                                <span>✓</span>
                                            </a></li>
                                    </ul>
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="tab-content">
                                            <div id="wizard_Service" class="tab-pane" role="tabpanel">
                                                <h4 class="card-title " style="color:#005a4b;">General Information</h4>
                                                <input type="hidden" name="uid" value="<?php echo $user[0]['userId']; ?>" class="form-control">
                                                <div class="row">

                                                    <?php
                                                    if (!$user[0]['branchId']) {
                                                        $branches = $response->getBankBranches($user[0]['bankId']);

                                                        echo '
                          <div class="col-lg-6 mb-2">
                          <div class="mb-3">
                              <label class="text-label form-label">Branch *</label>
                              <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true" name="branch" required>
                             
                                  ';
                                                        if ($branches !== '') {
                                                            foreach ($branches as $row) {
                                                                echo '
                              <option value="' . $row['id'] . '">' . $row['name'] . ' - ' . $row['location'] . '</option>
                              
                              ';
                                                            }
                                                        } else {
                                                            echo '
                              <option readonly>No Branches Added yet</option>
                              ';
                                                        }

                                                        echo
                                                        '
                          
                              </select>
                          </div>
                          </div>
                          
                          ';
                                                    } else {
                                                        echo '

                            <input type="hidden" name="branch" value="' . $user[0]['branchId'] . '" class="form-control" >

                            
                            ';
                                                    }
                                                    ?>


                                                    <?php
                                                    if (isset($_GET['1'])) {
                                                        echo '
<div class="col-lg-6 mb-2">
<div class="mb-3">
    <label class="text-label form-label">Choose Saving Product
        *</label>

   

        <select class="form-control"
        id="oscategory" name="actype" required>
        <option selected=""></option>

   

        ';

                                                        foreach ($actypes as $row) {
                                                            echo '
                                                        <option value="' . $row['id'] . '">' . $row['ucode'] . ' - ' . $row['name'] . '</option>
                                                        
                                                        ';
                                                        }


                                                        echo
                                                        '

    </select>
</div>
</div>

';
                                                    } else {
                                                        echo '
                                                        <input type="hidden" name="actype" class="form-control" placeholder="" value="0">
                                                        
                                                        ';
                                                    }
                                                    ?>
                                                    <div class="col-lg-3 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">SMS Message
                                                                Consent*</label>

                                                            <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="message" style="display: none;" required>
                                                                <option value="1">Yes</option>
                                                                <option value="0" selected>No</option>

                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Gender *
                                                            </label>

                                                            <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="gender" style="display: none;" required>
                                                                <option selected="Other"></option>
                                                                <option value="Male">Male</option>
                                                                <option value="Female">Female</option>

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Marital Status</label>

                                                            <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="marital" style="display: none;">
                                                                <option value="not married" selected>Not Married</option>
                                                                <option value="married">Married</option>

                                                                <option value="divorced">Divorced</option>

                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">First Name*</label>
                                                            <input type="text" name="fname" class="form-control" placeholder="" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Last Name*</label>
                                                            <input type="text" name="lname" class="form-control" placeholder="" required>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">

                                                            <label class="text-label form-label">Level of Education *</label>

                                                            <select name="education_level" class="me-sm-2 default-select form-control wide" id="osector">
                                                               
                                                                <option value="" disabled selected>-- Choose an option --</option>
                                                                <option value="Primary Level">Primary Level</option>
                                                                <option value="Ordinary Level">O-Level (Ordinary Level)</option>
                                                                <option value="Advanced Level">A-Level (Advanced Level)</option>
                                                                <option value="Diploma">Diploma</option>
                                                                <option value="Bachelors">Bachelor's Degree</option>
                                                                <option value="Masters">Master's Degree</option>
                                                                <option value="Doctorate">Doctorate (PhD)</option>
                                                                <option value="No Formal Education">No Formal Education</option>

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Address Line 1</label>
                                                            <input type="text" name="address" class="form-control" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Address Line 2</label>
                                                            <input type="text" name="address2" class="form-control" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Country</label>
                                                            <!-- <input type="text" name="country" class="form-control" placeholder=""> -->
                                                            <select name="country" id="countries" class="form-control">
                                                                <option value="Afghanistan">Afghanistan</option>
                                                                <option value="Albania">Albania</option>
                                                                <option value="Algeria">Algeria</option>
                                                                <option value="Andorra">Andorra</option>
                                                                <option value="Angola">Angola</option>
                                                                <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                                                                <option value="Argentina">Argentina</option>
                                                                <option value="Armenia">Armenia</option>
                                                                <option value="Australia">Australia</option>
                                                                <option value="Austria">Austria</option>
                                                                <option value="Azerbaijan">Azerbaijan</option>
                                                                <option value="Bahamas">Bahamas</option>
                                                                <option value="Bahrain">Bahrain</option>
                                                                <option value="Bangladesh">Bangladesh</option>
                                                                <option value="Barbados">Barbados</option>
                                                                <option value="Belarus">Belarus</option>
                                                                <option value="Belgium">Belgium</option>
                                                                <option value="Belize">Belize</option>
                                                                <option value="Benin">Benin</option>
                                                                <option value="Bhutan">Bhutan</option>
                                                                <option value="Bolivia">Bolivia</option>
                                                                <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                                                                <option value="Botswana">Botswana</option>
                                                                <option value="Brazil">Brazil</option>
                                                                <option value="Brunei">Brunei</option>
                                                                <option value="Bulgaria">Bulgaria</option>
                                                                <option value="Burkina Faso">Burkina Faso</option>
                                                                <option value="Burundi">Burundi</option>
                                                                <option value="Cambodia">Cambodia</option>
                                                                <option value="Cameroon">Cameroon</option>
                                                                <option value="Canada">Canada</option>
                                                                <option value="Cape Verde">Cape Verde</option>
                                                                <option value="Central African Republic">Central African Republic</option>
                                                                <option value="Chad">Chad</option>
                                                                <option value="Chile">Chile</option>
                                                                <option value="China">China</option>
                                                                <option value="Colombia">Colombia</option>
                                                                <option value="Comoros">Comoros</option>
                                                                <option value="Congo">Congo</option>
                                                                <option value="Costa Rica">Costa Rica</option>
                                                                <option value="Croatia">Croatia</option>
                                                                <option value="Cuba">Cuba</option>
                                                                <option value="Cyprus">Cyprus</option>
                                                                <option value="Czech Republic">Czech Republic</option>
                                                                <option value="Denmark">Denmark</option>
                                                                <option value="Djibouti">Djibouti</option>
                                                                <option value="Dominica">Dominica</option>
                                                                <option value="Dominican Republic">Dominican Republic</option>
                                                                <option value="Ecuador">Ecuador</option>
                                                                <option value="Egypt">Egypt</option>
                                                                <option value="El Salvador">El Salvador</option>
                                                                <option value="Equatorial Guinea">Equatorial Guinea</option>
                                                                <option value="Eritrea">Eritrea</option>
                                                                <option value="Estonia">Estonia</option>
                                                                <option value="Ethiopia">Ethiopia</option>
                                                                <option value="Fiji">Fiji</option>
                                                                <option value="Finland">Finland</option>
                                                                <option value="France">France</option>
                                                                <option value="Gabon">Gabon</option>
                                                                <option value="Gambia">Gambia</option>
                                                                <option value="Georgia">Georgia</option>
                                                                <option value="Germany">Germany</option>
                                                                <option value="Ghana">Ghana</option>
                                                                <option value="Greece">Greece</option>
                                                                <option value="Grenada">Grenada</option>
                                                                <option value="Guatemala">Guatemala</option>
                                                                <option value="Guinea">Guinea</option>
                                                                <option value="Guinea-Bissau">Guinea-Bissau</option>
                                                                <option value="Guyana">Guyana</option>
                                                                <option value="Haiti">Haiti</option>
                                                                <option value="Honduras">Honduras</option>
                                                                <option value="Hungary">Hungary</option>
                                                                <option value="Iceland">Iceland</option>
                                                                <option value="India">India</option>
                                                                <option value="Indonesia">Indonesia</option>
                                                                <option value="Iran">Iran</option>
                                                                <option value="Iraq">Iraq</option>
                                                                <option value="Ireland">Ireland</option>
                                                                <option value="Israel">Israel</option>
                                                                <option value="Italy">Italy</option>
                                                                <option value="Jamaica">Jamaica</option>
                                                                <option value="Japan">Japan</option>
                                                                <option value="Jordan">Jordan</option>
                                                                <option value="Kazakhstan">Kazakhstan</option>
                                                                <option value="Kenya">Kenya</option>
                                                                <option value="Kiribati">Kiribati</option>
                                                                <option value="Kuwait">Kuwait</option>
                                                                <option value="Kyrgyzstan">Kyrgyzstan</option>
                                                                <option value="Laos">Laos</option>
                                                                <option value="Latvia">Latvia</option>
                                                                <option value="Lebanon">Lebanon</option>
                                                                <option value="Lesotho">Lesotho</option>
                                                                <option value="Liberia">Liberia</option>
                                                                <option value="Libya">Libya</option>
                                                                <option value="Liechtenstein">Liechtenstein</option>
                                                                <option value="Lithuania">Lithuania</option>
                                                                <option value="Luxembourg">Luxembourg</option>
                                                                <option value="Madagascar">Madagascar</option>
                                                                <option value="Malawi">Malawi</option>
                                                                <option value="Malaysia">Malaysia</option>
                                                                <option value="Maldives">Maldives</option>
                                                                <option value="Mali">Mali</option>
                                                                <option value="Malta">Malta</option>
                                                                <option value="Marshall Islands">Marshall Islands</option>
                                                                <option value="Mauritania">Mauritania</option>
                                                                <option value="Mauritius">Mauritius</option>
                                                                <option value="Mexico">Mexico</option>
                                                                <option value="Micronesia">Micronesia</option>
                                                                <option value="Moldova">Moldova</option>
                                                                <option value="Monaco">Monaco</option>
                                                                <option value="Mongolia">Mongolia</option>
                                                                <option value="Montenegro">Montenegro</option>
                                                                <option value="Morocco">Morocco</option>
                                                                <option value="Mozambique">Mozambique</option>
                                                                <option value="Myanmar">Myanmar</option>
                                                                <option value="Namibia">Namibia</option>
                                                                <option value="Nauru">Nauru</option>
                                                                <option value="Nepal">Nepal</option>
                                                                <option value="Netherlands">Netherlands</option>
                                                                <option value="New Zealand">New Zealand</option>
                                                                <option value="Nicaragua">Nicaragua</option>
                                                                <option value="Niger">Niger</option>
                                                                <option value="Nigeria">Nigeria</option>
                                                                <option value="North Macedonia">North Macedonia</option>
                                                                <option value="Norway">Norway</option>
                                                                <option value="Oman">Oman</option>
                                                                <option value="Pakistan">Pakistan</option>
                                                                <option value="Palau">Palau</option>
                                                                <option value="Panama">Panama</option>
                                                                <option value="Papua New Guinea">Papua New Guinea</option>
                                                                <option value="Paraguay">Paraguay</option>
                                                                <option value="Peru">Peru</option>
                                                                <option value="Philippines">Philippines</option>
                                                                <option value="Poland">Poland</option>
                                                                <option value="Portugal">Portugal</option>
                                                                <option value="Qatar">Qatar</option>
                                                                <option value="Romania">Romania</option>
                                                                <option value="Russia">Russia</option>
                                                                <option value="Rwanda">Rwanda</option>
                                                                <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                                                                <option value="Saint Lucia">Saint Lucia</option>
                                                                <option value="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
                                                                <option value="Samoa">Samoa</option>
                                                                <option value="San Marino">San Marino</option>
                                                                <option value="Sao Tome and Principe">Sao Tome and Principe</option>
                                                                <option value="Saudi Arabia">Saudi Arabia</option>
                                                                <option value="Senegal">Senegal</option>
                                                                <option value="Serbia">Serbia</option>
                                                                <option value="Seychelles">Seychelles</option>
                                                                <option value="Sierra Leone">Sierra Leone</option>
                                                                <option value="Singapore">Singapore</option>
                                                                <option value="Slovakia">Slovakia</option>
                                                                <option value="Slovenia">Slovenia</option>
                                                                <option value="Solomon Islands">Solomon Islands</option>
                                                                <option value="Somalia">Somalia</option>
                                                                <option value="South Africa">South Africa</option>
                                                                <option value="South Sudan">South Sudan</option>
                                                                <option value="Spain">Spain</option>
                                                                <option value="Sri Lanka">Sri Lanka</option>
                                                                <option value="Sudan">Sudan</option>
                                                                <option value="Suriname">Suriname</option>
                                                                <option value="Sweden">Sweden</option>
                                                                <option value="Switzerland">Switzerland</option>
                                                                <option value="Syria">Syria</option>
                                                                <option value="Tajikistan">Tajikistan</option>
                                                                <option value="Tanzania">Tanzania</option>
                                                                <option value="Thailand">Thailand</option>
                                                                <option value="Timor-Leste">Timor-Leste</option>
                                                                <option value="Togo">Togo</option>
                                                                <option value="Tonga">Tonga</option>
                                                                <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                                                                <option value="Tunisia">Tunisia</option>
                                                                <option value="Turkey">Turkey</option>
                                                                <option value="Turkmenistan">Turkmenistan</option>
                                                                <option value="Tuvalu">Tuvalu</option>
                                                                <option value="Uganda" selected>Uganda</option>
                                                                <option value="Ukraine">Ukraine</option>
                                                                <option value="United Arab Emirates">United Arab Emirates</option>
                                                                <option value="United Kingdom">United Kingdom</option>
                                                                <option value="United States">United States</option>
                                                                <option value="Uruguay">Uruguay</option>
                                                                <option value="Uzbekistan">Uzbekistan</option>
                                                                <option value="Vanuatu">Vanuatu</option>
                                                                <option value="Vatican City">Vatican City</option>
                                                                <option value="Venezuela">Venezuela</option>
                                                                <option value="Vietnam">Vietnam</option>
                                                                <option value="Yemen">Yemen</option>
                                                                <option value="Zambia">Zambia</option>
                                                                <option value="Zimbabwe">Zimbabwe</option>
                                                            </select>

                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Region</label>

                                                            <select name="region" id="regions" class="form-control">
                                                                <option value="Central">Central</option>
                                                                <option value="Eastern">Eastern</option>
                                                                <option value="Northern">Northern</option>
                                                                <option value="Western">Western</option>
                                                            </select>

                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">District</label>

                                                            <select name="district" id="district" class="form-control">
                                                                <option value="Abim">Abim</option>
                                                                <option value="Adjumani">Adjumani</option>
                                                                <option value="Agago">Agago</option>
                                                                <option value="Alebtong">Alebtong</option>
                                                                <option value="Amolatar">Amolatar</option>
                                                                <option value="Amudat">Amudat</option>
                                                                <option value="Amuria">Amuria</option>
                                                                <option value="Amuru">Amuru</option>
                                                                <option value="Apac">Apac</option>
                                                                <option value="Arua">Arua</option>
                                                                <option value="Budaka">Budaka</option>
                                                                <option value="Bududa">Bududa</option>
                                                                <option value="Bugiri">Bugiri</option>
                                                                <option value="Bugweri">Bugweri</option>
                                                                <option value="Buhweju">Buhweju</option>
                                                                <option value="Buikwe">Buikwe</option>
                                                                <option value="Bukedea">Bukedea</option>
                                                                <option value="Bukomansimbi">Bukomansimbi</option>
                                                                <option value="Bukwo">Bukwo</option>
                                                                <option value="Bulambuli">Bulambuli</option>
                                                                <option value="Buliisa">Buliisa</option>
                                                                <option value="Bundibugyo">Bundibugyo</option>
                                                                <option value="Bunyangabu">Bunyangabu</option>
                                                                <option value="Bushenyi">Bushenyi</option>
                                                                <option value="Busia">Busia</option>
                                                                <option value="Butaleja">Butaleja</option>
                                                                <option value="Butambala">Butambala</option>
                                                                <option value="Butebo">Butebo</option>
                                                                <option value="Buvuma">Buvuma</option>
                                                                <option value="Buyende">Buyende</option>
                                                                <option value="Dokolo">Dokolo</option>
                                                                <option value="Gomba">Gomba</option>
                                                                <option value="Gulu">Gulu</option>
                                                                <option value="Hoima">Hoima</option>
                                                                <option value="Ibanda">Ibanda</option>
                                                                <option value="Iganga">Iganga</option>
                                                                <option value="Isingiro">Isingiro</option>
                                                                <option value="Jinja">Jinja</option>
                                                                <option value="Kaabong">Kaabong</option>
                                                                <option value="Kabale">Kabale</option>
                                                                <option value="Kabarole">Kabarole</option>
                                                                <option value="Kaberamaido">Kaberamaido</option>
                                                                <option value="Kagadi">Kagadi</option>
                                                                <option value="Kakumiro">Kakumiro</option>
                                                                <option value="Kalangala">Kalangala</option>
                                                                <option value="Kaliro">Kaliro</option>
                                                                <option value="Kalungu">Kalungu</option>
                                                                <option value="Kampala">Kampala</option>
                                                                <option value="Kamuli">Kamuli</option>
                                                                <option value="Kamwenge">Kamwenge</option>
                                                                <option value="Kanungu">Kanungu</option>
                                                                <option value="Kapchorwa">Kapchorwa</option>
                                                                <option value="Kapelebyong">Kapelebyong</option>
                                                                <option value="Karenga">Karenga</option>
                                                                <option value="Kasanda">Kasanda</option>
                                                                <option value="Kasese">Kasese</option>
                                                                <option value="Katakwi">Katakwi</option>
                                                                <option value="Kayunga">Kayunga</option>
                                                                <option value="Kazo">Kazo</option>
                                                                <option value="Kibaale">Kibaale</option>
                                                                <option value="Kiboga">Kiboga</option>
                                                                <option value="Kibuku">Kibuku</option>
                                                                <option value="Kiruhura">Kiruhura</option>
                                                                <option value="Kiryandongo">Kiryandongo</option>
                                                                <option value="Kisoro">Kisoro</option>
                                                                <option value="Kitagwenda">Kitagwenda</option>
                                                                <option value="Kitgum">Kitgum</option>
                                                                <option value="Koboko">Koboko</option>
                                                                <option value="Kole">Kole</option>
                                                                <option value="Kotido">Kotido</option>
                                                                <option value="Kumi">Kumi</option>
                                                                <option value="Kwania">Kwania</option>
                                                                <option value="Kween">Kween</option>
                                                                <option value="Kyankwanzi">Kyankwanzi</option>
                                                                <option value="Kyegegwa">Kyegegwa</option>
                                                                <option value="Kyenjojo">Kyenjojo</option>
                                                                <option value="Kyotera">Kyotera</option>
                                                                <option value="Lamwo">Lamwo</option>
                                                                <option value="Lira">Lira</option>
                                                                <option value="Luuka">Luuka</option>
                                                                <option value="Luwero">Luwero</option>
                                                                <option value="Lwengo">Lwengo</option>
                                                                <option value="Lyantonde">Lyantonde</option>
                                                                <option value="Manafwa">Manafwa</option>
                                                                <option value="Maracha">Maracha</option>
                                                                <option value="Masaka">Masaka</option>
                                                                <option value="Masindi">Masindi</option>
                                                                <option value="Mayuge">Mayuge</option>
                                                                <option value="Mbale">Mbale</option>
                                                                <option value="Mbarara">Mbarara</option>
                                                                <option value="Mitooma">Mitooma</option>
                                                                <option value="Mityana">Mityana</option>
                                                                <option value="Moroto">Moroto</option>
                                                                <option value="Moyo">Moyo</option>
                                                                <option value="Mpigi">Mpigi</option>
                                                                <option value="Mubende">Mubende</option>
                                                                <option value="Mukono">Mukono</option>
                                                                <option value="Nabilatuk">Nabilatuk</option>
                                                                <option value="Nakapiripirit">Nakapiripirit</option>
                                                                <option value="Nakaseke">Nakaseke</option>
                                                                <option value="Nakasongola">Nakasongola</option>
                                                                <option value="Namayingo">Namayingo</option>
                                                                <option value="Namisindwa">Namisindwa</option>
                                                                <option value="Namutumba">Namutumba</option>
                                                                <option value="Napak">Napak</option>
                                                                <option value="Nebbi">Nebbi</option>
                                                                <option value="Ngora">Ngora</option>
                                                                <option value="Ntoroko">Ntoroko</option>
                                                                <option value="Ntungamo">Ntungamo</option>
                                                                <option value="Nwoya">Nwoya</option>
                                                                <option value="Obongi">Obongi</option>
                                                                <option value="Omoro">Omoro</option>
                                                                <option value="Otuke">Otuke</option>
                                                                <option value="Oyam">Oyam</option>
                                                                <option value="Pader">Pader</option>
                                                                <option value="Pakwach">Pakwach</option>
                                                                <option value="Pallisa">Pallisa</option>
                                                                <option value="Rakai">Rakai</option>
                                                                <option value="Rubanda">Rubanda</option>
                                                                <option value="Rubirizi">Rubirizi</option>
                                                                <option value="Rukiga">Rukiga</option>
                                                                <option value="Rukungiri">Rukungiri</option>
                                                                <option value="Sembabule">Sembabule</option>
                                                                <option value="Serere">Serere</option>
                                                                <option value="Sheema">Sheema</option>
                                                                <option value="Sironko">Sironko</option>
                                                                <option value="Soroti">Soroti</option>
                                                                <option value="Tororo">Tororo</option>
                                                                <option value="Wakiso">Wakiso</option>
                                                                <option value="Yumbe">Yumbe</option>
                                                                <option value="Zombo">Zombo</option>
                                                            </select>

                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Sub-County</label>
                                                            <input type="text" name="subcounty" class="form-control" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Parish</label>
                                                            <input type="text" name="parish" class="form-control" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Village</label>
                                                            <input type="text" name="village" class="form-control" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Primary Phone
                                                                Number* (Include Country Code e.g 256707xxxxxx)</label>
                                                            <input type="text" name="primaryCellPhone" class="form-control" placeholder="" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Any Other Phone
                                                                Number (Include Country Code e.g 256707xxxxxx)</label>
                                                            <input type="text" class="form-control" placeholder="" name="secondaryCellPhone">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Email Address</label>
                                                            <input type="email" class="form-control" placeholder="" name="email">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">NIN</label>
                                                            <input type="text" name="nin" class="form-control" placeholder="" maxlength="14">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Date of Birth</label>
                                                            <input type="date" class="form-control" name="dob" placeholder="" value="<?php echo date('Y-m-d'); ?>">
                                                        </div>
                                                    </div>



                                                </div>
                                            </div>
                                            <div id="wizard_Time" class="tab-pane" role="tabpanel">

                                                <h4 class="card-title " style="color:#005a4b;">Business Information</h4>
                                                <div class="row">
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Business Name</label>
                                                            <input type="text" name="bname" class="form-control" placeholder="">
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Choose Business
                                                                Type</label>

                                                            <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="businesstype" style="display: none;">
                                                                <option selected=""></option>
                                                                <option value="Registered">Registered</option>
                                                                <option value="Not Registered">Not Registered</option>

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Business Registration
                                                                Number #</label>
                                                            <input type="text" name="businessreg" class="form-control" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Address Line 1</label>
                                                            <input type="text" name="baddress" class="form-control" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Address Line 2</label>
                                                            <input type="text" name="baddress2" class="form-control" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Country</label>
                                                            <input type="text" name="businesscountry" class="form-control" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">City</label>
                                                            <input type="text" name="businessCity" class="form-control" placeholder="">
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 mb-2 mt-3">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Expected Income Per Month</label>
                                                            <input type="text" class="form-control comma_separated" placeholder="" value="0" name="income" required>
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                            <div id="wizard_occupation" class="tab-pane" role="tabpanel">

                                                <h4 class="card-title " style="color:#005a4b;">Occupation Details</h4>
                                                <div class="row">
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Economic Sector</label>

                                                            <select class="form-control " id="osector" name="ocsector">

                                                                <?php
                                                                $sectorsm = $response->getOccupationOptions($user[0]['bankId'], $user[0]['branchId'], 'sectors');

                                                                if ($sectorsm) {
                                                                    foreach ($sectorsm as $sector) {
                                                                        echo '
                                                                        <option value="' . $sector['osid'] . '">' . $sector['os_name'] . '</option>
                                                                        ';
                                                                    }
                                                                }
                                                                ?>
                                                                <option value="0" selected>Others (Specify below)</option>

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2" style="display: none;" id="sect_other">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Enter the Economic Sector</label>
                                                            <input type="text" name="sect_other" class="form-control" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Occupation Category</label>

                                                            <select class="form-control " id="ocategory" name="ocategory">

                                                                <?php
                                                                $sectorsy = $response->getOccupationOptions($user[0]['bankId'], $user[0]['branchId'], 'category');

                                                                if ($sectorsy) {
                                                                    foreach ($sectorsy as $sector) {
                                                                        echo '
                                                                        <option value="' . $sector['oscid'] . '">' . $sector['osc_name'] . '</option>
                                                                        ';
                                                                    }
                                                                }
                                                                ?>
                                                                <option value="0" selected>Others (Specify below)</option>

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2" style="display: none;" id="cat_other">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Enter the Occupation Category</label>
                                                            <input type="text" name="cat_other" class="form-control" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Occupation Sub-Category</label>

                                                            <select class="form-control " id="oscategory" name="oscategory">

                                                                <?php
                                                                $sectors = $response->getOccupationOptions($user[0]['bankId'], $user[0]['branchId'], 'subcategory');

                                                                if ($sectors) {
                                                                    foreach ($sectors as $sector) {
                                                                        echo '
                                                                        <option value="' . $sector['ocid'] . '">' . $sector['oc_name'] . '</option>
                                                                        ';
                                                                    }
                                                                }
                                                                ?>
                                                                <option value="0" selected>Others (Specify below)</option>

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2" style="display: none;" id="sub_other">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Enter the Occupation Sub-Category</label>
                                                            <input type="text" name="sub_other" class="form-control" placeholder="">
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Occupation / Profession Name</label>
                                                            <input type="text" name="prof" class="form-control" placeholder="">
                                                        </div>
                                                    </div>




                                                </div>
                                            </div>

                                            <div id="wizard_Details" class="tab-pane" role="tabpanel">

                                                <h4 class="card-title " style="color:#005a4b;">Family / Next of Kin
                                                    Information</h4>
                                                <div class="row">
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Next of Kin's
                                                                Name</label>
                                                            <input type="text" name="kname" class="form-control" placeholder="">
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Next of Kin's Phone
                                                                Number</label>
                                                            <input type="text" name="kinphone" class="form-control" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Physical
                                                                Address</label>
                                                            <input type="text" name="kphysicaladdress" class="form-control" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Next of Kin's
                                                                NIN</label>
                                                            <input type="text" name="knin" class="form-control" placeholder="" maxlength="14">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 mb-3">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Describe the
                                                                relationship with Client?</label>
                                                            <input type="text" name="relationship" class="form-control" placeholder="">
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                            <div id="wizard_disability" class="tab-pane" role="tabpanel">

                                                <h4 class="card-title " style="color:#005a4b;">Disability Status</h4>
                                                <div class="row">
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Disability Status</label>

                                                            <select class="form-control " id="authby" name="disability_status" required>


                                                                <option value="yes">Yes (I'm Disabled)</option>
                                                                <option value="no" selected>No</option>

                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Disability Category</label>

                                                            <select class="form-control " id="disability" name="disability_category" required>


                                                                <option value="none" selected>None</option>
                                                                <option value="Deafness">Deafness</option>
                                                                <option value="Blindness">Blindness</option>
                                                                <option value="Specific learning disability">Specific learning disability</option>
                                                                <option value="Emotional disturbance">Emotional disturbance</option>
                                                                <option value="Deafblind">Deafblind</option>
                                                                <option value="Cognitive or learning disabilities">Cognitive or learning disabilities</option>
                                                                <option value="Traumatic brain injury">Traumatic brain injury</option>
                                                                <option value="Intellectual disability">Intellectual disability</option>
                                                                <option value="Speech or language impairment">Speech or language impairment</option>
                                                                <option value="Multiple disabilities">Multiple disabilities</option>
                                                                <option value="Vision">Vision</option>
                                                                <option value="Paralysis">Paralysis</option>
                                                                <option value="Autism">Autism</option>
                                                                <option value="Orthopedic impairment">Orthopedic impairment</option>
                                                                <option value="Other health Impairment">Other health Impairment</option>
                                                                <option value="Developmental delay">Developmental delay</option>
                                                                <option value="Psychiatric">Psychiatric</option>
                                                                <option value="Physical">Physical</option>
                                                                <option value="Others">Others</option>

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2" id="disabled_cat_other">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">If you selected Others above</label>
                                                            <input type="text" name="disabled_cat_other" class="form-control" placeholder="">
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Describe the Disability Details of the Client</label>
                                                            <textarea cols="10" name="disability_details" class="form-control" placeholder="" required>NIL</textarea>
                                                        </div>
                                                    </div>






                                                </div>
                                            </div>

                                            <div id="wizard_confirm" class="tab-pane" role="tabpanel">
                                                <h4 class="card-title " style="color:#005a4b;">Confirm & Submit</h4>
                                                <div class="row ">
                                                    <div class="col-lg-12 mb-3">
                                                        <div class="mb-3">
                                                            <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--**********************************
            Content body end
        ***********************************-->


            <!--**********************************
            Footer start
        ***********************************-->
            <?php include('includes/footer.php'); ?>
            <!--**********************************
            Footer end
        ***********************************-->

            <!--**********************************
           Support ticket button start
        ***********************************-->

            <!--**********************************
           Support ticket button end
        ***********************************-->


        </div>
        <!--**********************************
        Main wrapper end
    ***********************************-->

        <!--**********************************
        Scripts
    ***********************************-->
        <!-- Required vendors -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
        <!-- <script src="./js/webcamjs/webcam.js"></script> -->
        <script src="./js/main.js"></script>
        <?php
        include('includes/bottom_scripts.php');
        ?>

        <script>
            $('.is-back-btn').each(function() {
                $(this).addClass('hide');
                if (history.length) {
                    $(this).removeClass('hide');
                }
            });

            $('body').on('click', '.is-back-btn', function(event) {
                event.preventDefault();
                history.back();
            });
        </script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#smartwizard').smartWizard();


            });
        </script>
        <script type="text/javascript">
            $(document).ready(function() {
                pay_method_change();
            });
        </script>

        <script>
            function pay_method_change() {
                var sect_other = $('#sect_other');
                var cat_other = $('#cat_other');
                var subs_other = $('#sub_other');

                $('#osector').change(function() {

                    if ($(this).find('option:selected').val() == 0) {
                        sect_other.show();
                    } else {
                        sect_other.hide();
                    }


                });

                $('#ocategory').change(function() {

                    if ($(this).find('option:selected').val() == 0) {
                        cat_other.show();
                    } else {
                        cat_other.hide();
                    }


                });

                $('#oscategory').change(function() {

                    if ($(this).find('option:selected').val() == 0) {
                        subs_other.show();
                    } else {
                        subs_other.hide();
                    }


                });





            }
            // -------------end --------------
        </script>


</body>

</html>