<script>
var tmt_update_interval = 3000;

var tmt_event_box_class = 'sb_event_box';
var sport_url_attr = 'data-sport_url';
var event_url_attr = 'data-event_url';
var event_id_attr = 'data-event_id';

var tmt_market_box_class = 'sb_market_box';
var market_url_attr = 'data-market_url';

var tmt_outcome_box_class = 'sb_outcome_box';
var outcome_url_attr = 'data-outcome_url';

var tmt_outcome_name_class = 'sb_outcome_name';

var tmt_odds_val_class = 'sb_odds_val';

var tmt_spark_class = 'flashy';

var tmt_sb_disabled = 'sb_disabled';

var tmt_market_count = 'sb_event_market_count';

var bs_item_class = 'user_selection';

var tmt_odds_json_base_url = decodeURIComponent('{{\Storage::disk("sr_s3")->url("sigma/json/:event_id.json")}}');

var sport_markets = {
  'default' : { 'market_id' : [ 1 ] },
  'srsport21' : { 'market_id' : [ 340 ] },
  'srsport1' : { 'market_id' : [ 1 ] },
  'srsport2' : { 'market_id' : [ 219 ] },
  'srsport5' : { 'market_id' : [ 186 ] },
  'srsport22' : { 'market_id' : [ 186 ] },
  'srsport117' : { 'market_id' : [ 186 ] },
  'srsport23' : { 'market_id' : [ 186 ] },
  'srsport13' : { 'market_id' : [ 186 ] },
  'srsport19' : { 'market_id' : [ 186 ] },
  'srsport20' : { 'market_id' : [ 186 ] },
  'srsport3' : { 'market_id' : [ 251 ] },
};

var tmt_lock_html = `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M19 11H5C3.89543 11 3 11.8954 3 13V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V13C21 11.8954 20.1046 11 19 11Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M7 11V7C7 5.67392 7.52678 4.40215 8.46447 3.46447C9.40215 2.52678 10.6739 2 12 2C13.3261 2 14.5979 2.52678 15.5355 3.46447C16.4732 4.40215 17 5.67392 17 7V11" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
<rect x="11" y="14" width="2" height="5" rx="1" fill="white"/>
</svg>`;



setInterval(fetchTmtList,tmt_update_interval);


function fetchTmtList() {
  $('.'+tmt_event_box_class+':visible').each(function(i,e){
    if (checkTmtIsInViewport($(this))) {
      getTmtMarketsOdds($(this).attr(event_id_attr));
    }
  });
}



function filterTmtElementUrl(str) {
  str=str.replaceAll('_','');
  str=str.replaceAll('/','');
  str=str.replaceAll('{','');
  str=str.replaceAll('=','');
  str=str.replaceAll(', ','');
  str=str.replaceAll('}','');
  str=str.replaceAll('.','');
  str=str.replaceAll(':','');
  str=str.replaceAll('+','');
  return str;
}



function checkTmtIsInViewport(that) {
  if ($(that).length == 0) {
    return false;
  }
  var elementTop = $(that).offset().top;
  var elementBottom = elementTop + $(that).outerHeight();
  var viewportTop = $(window).scrollTop();
  var viewportBottom = viewportTop + $(window).height();
  return elementBottom > viewportTop && elementTop < viewportBottom;
}



function getTmtMarketsOdds(event_id) {

  let event_id_ = event_id.replaceAll(':','_');

  let sport_url = market_url = outcome_url = '';

  let event_url = filterTmtElementUrl(event_id);

  let event_odds_url = tmt_odds_json_base_url.replace(':event_id',event_id_);

  let odd_len=0;

  let tmt_event_box = $('.'+tmt_event_box_class+'['+event_url_attr+'='+event_url+']');

  $.ajax({
    type: 'get',
    url: event_odds_url,
    cache: false,
    success: function(data){

      data = JSON.parse(data);

      sport_url = tmt_event_box.attr(sport_url_attr);

      $.each(data.odds_data,function(i,m){

        if (m == null) {
          return;
        }

        market_url=filterTmtElementUrl(event_id+'/'+m.m+'/'+m.s);

        tmt_market_box = $('.'+tmt_market_box_class+'['+market_url_attr+'='+market_url+']');

        inBetSlip = $('.'+bs_item_class+'['+market_url_attr+'='+market_url+']').length;
        if (inBetSlip > 0) {
          try {
            parseMarketBetslip(event_id,m)
          } catch (e) {
            console.log('Betslip parse package is missing from your project. Please install to update betslip data');
          }
        }

        if (m.t != 'Active') {
          tmt_market_box.find('.'+tmt_odds_val_class).html(tmt_lock_html);
          tmt_market_box.find('.'+tmt_outcome_box_class).addClass(tmt_sb_disabled);
        }

        if (m.t == 'Active') {
          if (sport_markets[sport_url] && sport_markets[sport_url].market_id.includes(m.m)) {
            updateTmtMarket(event_id,m);
          } else if (!sport_markets[sport_url] && sport_markets['default'].market_id.includes(m.m)) {
            updateTmtMarket(event_id,m);
          }
        }

        if (m.t == 'Active' || m.t == 'Suspended') {
          odd_len+=1;
        }

      });

      $('.'+tmt_event_box_class+'['+event_url_attr+'='+event_url+']').find('.'+tmt_market_count).html('+'+odd_len);

    }
  });

}

function updateTmtMarket(event_id,m) {

  let market_url = filterTmtElementUrl(event_id+m.m+m.s);

  $.each(m.o,function(io,o){

      outcome_url = '';
      outcome_url+=market_url+'/'+o.o;
      outcome_url=filterTmtElementUrl(outcome_url);

      tmt_outcome_box = $('.'+tmt_outcome_box_class+'['+outcome_url_attr+'='+outcome_url+']');

      old_odds = tmt_outcome_box.find('.'+tmt_odds_val_class).html();

      if(o.d != old_odds){

        tmt_outcome_box.find('.'+tmt_odds_val_class).html(o.d);

        tmt_outcome_box.find('.'+tmt_outcome_name_class).html(o.n);

        sparkTmtOutcomeBox(tmt_outcome_box);

      }

      tmt_outcome_box.removeClass(tmt_sb_disabled)

    });

}

function sparkTmtOutcomeBox(tmt_outcome_box) {
  $(tmt_outcome_box).addClass(tmt_spark_class)
  setTimeout(function(){
    $(tmt_outcome_box).removeClass(tmt_spark_class);
  },600);
}

</script>
