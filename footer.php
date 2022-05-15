<footer>
      <div class="copy">
        <p>Copyright(c) 2005-2022 ikefukuro_40 . All Rights Reserved.</p>
      </div>
</footer>
<script src="jquery-3.5.1.min.js"></script>
<script src="jquery.minicalendar.js"></script>
<script type="text/javascript">
(function($) {
	$(function() {


    // console.log($(location).attr('tgtyyyymm'));
    console.log(location.search.substring(1).split('=')[1]);

		// $('#mini-calendar').miniCalendar('2022/04');
    $('#mini-calendar').miniCalendar(location.search.substring(1).split('=')[1]);

    $("#mini-calendar tr td").click(function(){
        var tgtday = ('0'+$(this).find(".calendar-day-number").text()).slice(-2);
        var tgtyearmonth = $(".calendar-year-month").text().replace('年','/').replace('月','');
        // var tgturl = "https://ikefukuro40.tech/dailycheck/list.php?yearmonth="+tgtyearmonth+"&day="+tgtday;
        // var tgturl = "https://ikefukuro40.tech/dailycheck/list.php?yearmonth="+tgtyearmonth+"&day="+tgtday;
        if(tgtday=='' || tgtyearmonth==''){
           return;
        }
        var tgturl = "https://ikefukuro40.tech/dailycheck/list.php?yearmonth="+tgtyearmonth+"&day="+tgtday;
        location.href = tgturl;
    });
	});
})(jQuery);
</script>
<script src="humberger.js"></script>