<input type="hidden" name="extra_price" id="extra_price" value="0"/>
<div class="row">
    <div class="col-lg-11">
        <h5><b>Summary</b></h5>
        <hr/>
    </div>
</div>
<div class="row">
    <div class="col-lg-11">
        <div class="col-md-6" id="course_title"><?php echo $model["title"]; ?></div>
        <div class="col-md-6 text-right"><span id="cart_price"><?php echo $model["cart_price"]; ?></span></div>
    </div>
</div>
<div class="row">
    <div class="col-lg-11">
        <hr/>
    </div>
</div>
<div class="extra_price_row row hidden">
    <div class="col-lg-11">
        <div class="col-md-6" id="cart_extra_price_label"></div>
        <div class="col-md-6 text-right"><span id="cart_extra_price"><?php echo $model["extra_price"]; ?></span></div>
    </div>
</div>
<div class="extra_price_row row hidden">
    <div class="col-lg-11">
        <hr/>
    </div>
</div>
<div class="row">
    <div class="col-lg-11">
        <div class="col-md-6">Totalen</div>
        <div class="col-md-6 text-right bold-total"><span id="cart_total"><?php echo $model["cart_total"]; ?></span>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-11">
        <hr/>
    </div>
</div>
<div class="row">
    <div class="col-lg-4">
        <button class="btn" type="submit">Bevestigen</button>
    </div>
</div>