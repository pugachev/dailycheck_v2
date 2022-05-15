/*
 * jQuery Mini Calendar
 * https://github.com/k-ishiwata/jQuery.MiniCalendar
 *
 * Copyright 2016, k.ishiwata
 * http://www.webopixel.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function($) {



  $.wop = $.wop || {};
  $.wop.miniCalendar = function(targets,option){
    this.opts = $.extend({},$.wop.miniCalendar.defaults,option);
    this.ele = targets;

    // console.log(option);


    // jsonファイルから読み込んだデータを入れる変数
    this.events = {};

    this.month = "";
    this.year = "";
    this.holiday = "";


    if(option!=''&&option.length>0){
      this.date = option;
      this.year = option.split('/')[0];
      this.month =  option.split('/')[1];
      this.prevyyyyMM=this.year+'/'+('0'+(Number(this.month)-1)).slice(-2);
      this.nextyyyyMM=this.year+'/'+('0'+(Number(this.month)+1)).slice(-2);
    }else{
      this.date = new Date();
      //表示する年月
      this.year = this.year || new Date().getFullYear();
      this.month = this.month || new Date().getMonth()+1;
      this.prevyyyyMM=this.year+'/'+('0'+(Number(this.month)-1)).slice(-2);
      this.nextyyyyMM=this.year+'/'+('0'+(Number(this.month)+1)).slice(-2);
      this.month = ('0'+this.month).slice(-2);
    }


    console.log(this.prevyyyyMM);
    console.log(this.nextyyyyMM);

    this.prevURL = "https://ikefukuro40.tech/dailycheck/index.php?tgtyyyymm="+this.prevyyyyMM;
    this.nextURL = "https://ikefukuro40.tech/dailycheck/index.php?tgtyyyymm="+this.nextyyyyMM;
    // jsonファイルから読み込む
    this.loadData();



    this.createFrame();
    this.printType(this.year, this.month);
    // 取得したイベントを表示
    this.setEvent();
  };

  $.wop.miniCalendar.prototype = {

    // test: function(){
    //   console.log("test");
    // },

    /**
     * 枠を作成
     */
    createFrame : function() {
      // var tmpmonth = (this.month-1);
      var prevyyyyMM=this.year+'/'+('0'+(this.month-1)).slice(-2);
      // var tmpmonth = (this.month+1);
      var nextyyyyMM=this.year+'/'+('0'+(this.month+1)).slice(-2);
      // console.log(prevyyyyMM);
      // this.ele.append('<div class="calendar-head"><p class="prev">   前月へ   </p><p class="calendar-year-month"></p><p class="next">   翌月へ   </p></div>');
      this.ele.append('<div class="calendar-head"><a href="'+this.prevURL+'" class=prev ">   前月へ   </a><p class="calendar-year-month"></p><a href="'+this.nextURL+'" class=prev ">   翌月へ   </a>');
      // this.ele.append('<div class="calendar-head"><a href="https://ikefukuro40.tech/dailycheck/calendar.php?tgtyyyymm=2022/04">   前月へ   </a><p class="calendar-year-month"></p><a class=prev href="https://ikefukuro40.tech/dailycheck/calendar.php?tgtyyyymm="'+nextyyyyMM+'">   翌月へ   </a>');
      var outText = '<table><thead><tr>';
      for (var i = 0; i < this.opts.weekType.length; i++) {
        if (i === 0) {
          outText += '<th class="calendar-sun">';
        } else if (i === this.opts.weekType.length-1) {
          outText += '<th class="calendar-sat">';
        } else {
          outText += '<th>';  
        }
        
        outText += this.opts.weekType[i] +'</th>';
      }
      outText += '</thead><tbody></tbody></table>';
      this.ele.find('.calendar-head').after(outText);
    },

    /**
     * 日付・曜日の配置
     */
    printType : function(thisYear, thisMonth) {

      $(this.ele).find('.calendar-year-month').text(thisYear + '年' + thisMonth + '月');
      var thisDate = new Date(thisYear, thisMonth-1, 1);

      // 開始の曜日
      var startWeek = thisDate.getDay();

      var lastday = new Date(thisYear, thisMonth, 0).getDate();
      // 縦の数
      //var rowMax = Math.ceil((lastday + (startWeek+1)) / 7);
      var rowMax = Math.ceil((lastday + startWeek) / 7);

      var outText = '<tr>';
      var countDate = 1;
      // 最初の空白を出力
      for (var i = 0; i < startWeek; i++) {
        outText += '<td class="calendar-none">&nbsp;</td>';
      }
      for (var row = 0; row < rowMax; row++) {
        // 最初の行は曜日の最初から
        if (row == 0) {
          for (var col = startWeek; col < 7; col++) {
            outText += printTD(countDate, col);
            countDate++;
          }
        } else {
          // 2行目から
          outText += '<tr>';
          for (var col = 0; col < 7; col++) {
            if (lastday >= countDate) {
              outText += printTD(countDate, col);
            } else {
              outText += '<td class="calendar-none">&nbsp;</td>';
            }
            countDate++;
          }
        }
        outText += '</tr>';
      }
      $(this.ele).find('tbody').html(outText);

      function printTD(count, col) {
        var dayText = "";
        var tmpId = ' id="calender-id'+ count + '"';
        // 曜日classを割り当てる
        if (col === 0) tmpId += ' class="calendar-sun"';
        if (col === 6) tmpId += ' class="calendar-sat"';
        return '<td' + tmpId + '><i class="calendar-day-number">' + count + '</i><div class="calendar-labels">' + dayText + '</div></td>';
      }

      //今日の日付をマーク
      var toDay = new Date();
      if (thisYear === toDay.getFullYear()) {
        if (thisMonth === (toDay.getMonth()+1)) {
          var dateID = 'calender-id' + toDay.getDate();
          $(this.ele).find('#' + dateID).addClass('calendar-today');
        }
      }
    },
    /**
     * イベントの表示
     */
    setEvent : function() {
      for(var i = 0; i < this.events.length; i++) {
        var dateID = 'calender-id' + this.events[i].day;
        var getText = $('<textarea>' + this.events[i].title + '</textarea>');
        // typeがある場合classを付与
        var type = "";
        if (this.events[i].type) {
          type = '-' + this.events[i].type;
        }
        $(this.ele)
               .find('#' + dateID + ' .calendar-labels').append('<span class="calender-label' + type + '">' + getText.val() + '</span>');

      }

      // 休日
      for (var i=0; i<this.holiday.length; i++) {
        $(this.ele).find('#calender-id' + this.holiday[i]).addClass('calendar-holiday');
      }
    },

    /**
     * jsonファイルからデータを読み込む
     */
    loadData : function() {
      var self = this;
      var tgtyearmonth=self.year+'/'+self.month;
      $.ajax({
        type: "GET",
        url: "https://ikefukuro40.tech/dailycheck/calendar.php?tgtyyyymm="+tgtyearmonth,
        dataType: "json",
        async: false,
        cache: false,
        timeout: 10000,
        success: function(data){
          self.events = data.event;
          self.year = data.year;
          self.month = data.month;
          self.date = new Date(data.date);
          self.holiday = data.holiday;
        }
      });
    }
  };

  $.wop.miniCalendar.defaults = {
    weekType : ["日", "月", "火", "水", "木", "金", "土"],
    jsonData: 'event.json'
  };
  $.fn.miniCalendar = function(option){
    option = option || {};
    var api = new $.wop.miniCalendar(this,option);
    return option.api ? api : this;
  };
})(jQuery);
