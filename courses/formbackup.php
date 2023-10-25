 $options = "";
    $trainingradio = "";

    foreach ($courses as $course) {
        $options .= '<option value="' . date('d-m-Y', $course->date) . ' - ' . $course->location . '">' . date('d-m-Y', $course->date) . ' - ' . $course->location . '</option>';
        $trainingradio .= '<label class="label-training-form"><input type="radio" name ="course"   required value="' . date('d-m-Y', $course->date) . ' - ' . $course->location . '" />' . date('d-m-Y', $course->date) . ' - ' . $course->location . '</label>';
    }

    $form = '<form name="form_subscription" id="form_subscription" method="POST"><div class="vc_row-fluid rowform">
                                        <div class="vc_column_container vc_col-sm-12 formheader">Cursusdata + locatie</div>
    <div class="form-group">
            <label class="vc_column_container  vc_col-sm-3 control-label">Selecteer *</label>
            <div class="vc_column_container vc_col-sm-9">
                   ' . $trainingradio . '
            </div>
        </div>
                <div class="clear"></div>
        <div class="vc_column_container vc_col-sm-12 formheader">Klantgegevens</div>
        <div class="form-group">
            <label class="vc_column_container  vc_col-sm-3 control-label">Aanmelden als</label>
            <div class="vc_column_container  vc_col-sm-9">
                <label class="radio-inline"> <input class="radio_type" type="radio" name="iscompany" value="0" CHECKED>Particulier</label>
                <label class="radio-inline"> <input class="radio_type" type="radio" name="iscompany" value="1" >Zakelijk</label>
            </div>
        </div>
    </div>
    <div class="clear"></div>
    <div class="business">
        <div class="vc_row-fluid rowform">
            <div class="form-group">
                <label class="vc_column_container  vc_col-sm-3 control-label">Bedrijfsnaam*</label>
                <div class="vc_column_container  vc_col-sm-9">
                    <input type ="text" name="companyname" class="form-control businessfield" placeholder="Bedrijfsnaam" required="true"/>
                </div>
            </div>
        </div>
        <div class="clear"></div>
<!--
        <div class="vc_row-fluid rowform">
            <div class="form-group">
                <label class="vc_column_container  vc_col-sm-3 control-label">Branche</label>
                <div class="vc_column_container  vc_col-sm-9">
                        <select class="form-control businessfield" name="branche">
                            <option value="">Maak een keuze</option>
                            <option value="ZZP" >ZZP</option>
                            <option value="Asbest" >Asbest</option>
                            <option value="Bouw" >Bouw</option>
                            <option value="Evenementen" >Evenementen</option>
                            <option value="Groothandel" >Groothandel</option>
                            <option value="GW&W" >GW&W</option>
                            <option value="Haven & Overslag" >Haven & Overslag</option>
                            <option value="Horeca" >Horeca</option>
                            <option value="Opleiden" >Opleiden</option>
                            <option value="Overheid" >Overheid</option>
                            <option value="Reïntegratie" >Reïntegratie</option>
                            <option value="Steigerbouw" >Steigerbouw</option>
                            <option value="Transport & Logistiek" >Transport & Logistiek</option>
                            <option value="Uitzenden" >Uitzenden</option>
                            <option value="Overige" >Overige</option>
                        </select>
                </div>
            </div>
        </div>
-->
    </div>

    <div class="clear"></div>
    <div class="vc_row-fluid rowform">
        <div class="form-group">
            <label class="vc_column_container  vc_col-sm-3 control-label">Contactpersoon*</label>
            <div class="vc_column_container  vc_col-sm-9">
                <div class="vc_col-sm-6" style="padding-left:0px;">
                    <input type ="text" name="firstname" class="form-control"  placeholder="Voorletters" required="true"/>
                </div>
                <div class="vc_col-sm-4 hidden">
                    <input type ="hidden" name="middlename" class="form-control " placeholder="Tussenv." />
                </div>
                <div class="vc_col-sm-6" style="padding-right:0px;">
                    <input type ="text" name="lastname" class="form-control " placeholder="Achternaam" required="true"/>
                </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
    <div class="vc_row-fluid rowform">
        <div class="form-group">
            <label class="vc_column_container  vc_col-sm-3 control-label">Telefoon*</label>
            <div class="vc_column_container  vc_col-sm-9">
                <input type ="tel" name="phone" class="form-control number" placeholder="Telefoonnummer" required="true"/>
            </div>
        </div>
    </div>
    <div class="clear"></div>
    <div class="vc_row-fluid rowform">
        <div class="form-group">
            <label class="vc_column_container  vc_col-sm-3 control-label">Email*</label>
            <div class="vc_column_container  vc_col-sm-9">
                <input type = "email" name="email" class="form-control" placeholder="Email" required="true"/>
            </div>
        </div>
    </div>
    <div id="cursist">
        <div class="vc_column_container vc_col-sm-12 formheader">Cursist(en) gegevens</div>
        <div class="form-group">
            <label class="vc_column_container  vc_col-sm-3 control-label">Aantal *</label>
            <div class="vc_column_container  vc_col-sm-9"> 
                <input id="amount_cursists" name ="noCursists" class="form-control" value="1" type="number" min="1"  required/> 
            </div>
        </div>
        <div class="clear"></div>
        <div id="cusistswrapper">  
        </div>
    </div>
    <div class="clear"></div>
    <div id="payment" class="private">
        <div class="vc_row-fluid rowform">
            <div class="vc_column_container vc_col-sm-12 formheader">Betalen & versturen</div>
            <div class="form-group">
                <label class="vc_column_container  vc_col-sm-3 control-label">Betalen via</label>
                <div class="vc_column_container  vc_col-sm-9">
                    <label class="radio-inline"> <input class="radio_type2 privatefield" type="radio" name="payment" value="0" CHECKED>iDeal</label>
                     <label class="radio-inline"> <input class="radio_type2 privatefield" type="radio" name="payment" value="1">factuur</label>
                </div>
            </div>
        </div>
    </div>
    
    <div class="clear"></div>
    <div class="vc_row-fluid rowform"> 
        <div class="vc_column_container vc_col-sm-12"><input type="submit" class="btn btn-primary" id="submit_subscription" value="Inschrijven"></input></div>
    </div>
    <input type="hidden" name="eventform" value="1"></input>
    <input type="hidden" name="courseid" value="' . $id . '"></input>
    </form>
    ';

     $error = "<p class='error' style='color:red;'>Dit veld is vereist</p>";
    $form .= '<script>
        jQuery(document).ready(function(){
            
            jQuery(".number").keydown(function (e) {
        
            if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
                (e.keyCode >= 35 && e.keyCode <= 40)) {        
                    return;
                }
      
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        
            });
            
            jQuery(document).on("keydown",".number",function (e) {
            
            if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190,191]) !== -1 ||
                (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
                (e.keyCode >= 35 && e.keyCode <= 40)) {        
                    return;
                }
      
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        
            });
            
            
            jQuery("#submit_subscription").click(function(){
                jQuery("input").css("border-color","#ccc");
                jQuery(".error").remove();
                jQuery("form#form_subscription :input").each(function(){
                    
                    if(jQuery(this).prop("required") && jQuery(this).val()==""){ 
                        jQuery(this).css("border-color","red");
                        jQuery(this).parent().append( "' . $error . '" );  
                    }
                })
                
                 
            });
                 
        });
    </script>';