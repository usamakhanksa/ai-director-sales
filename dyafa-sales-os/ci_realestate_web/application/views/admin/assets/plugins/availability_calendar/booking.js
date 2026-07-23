
$(document).ready(function()
{
  /*
  $('#basic').calendar();
  
  $('#glob-data').calendar(
  {
	unavailable: ['*-*-8', '*-*-10']
  });
  
  $('#custom-first-day').calendar(
  {
	day_first: 2,
	unavailable: ['2014-07-10'],
	onSelectDate: function(date, month, year)
	{
	  alert([year, month, date].join('-') + ' is: ' + this.isAvailable(date, month, year));
	}
  });
  
  $('#custom-name').calendar(
  {
	day_name: ['CN', 'Hai', 'Ba', 'Tư', 'Năm', 'Sáu', 'Bảy'],
	month_name: ['Tháng Một', 'Tháng Hai', 'Tháng Ba', 'Tháng Tư', 'Tháng Năm', 'Tháng Sáu', 'Tháng Bảy', 'Tháng Tám', 'Tháng Chín', 'Tháng Mười', 'Tháng Mười Một', 'Tháng Mười Hai'],
	unavailable: ['2014-07-10']
  });
  
  $('#dynamic-data').calendar(
  {
	adapter: 'http://localhost/2014/jquery-availability-calendar/src/server/adapter.php'
  });
  */
  
  var prop_id = $('.booking_prop_list').find(":selected").val();
  $('#show-next-month').calendar(
  {
	num_next_month: 1,
	num_prev_month: 1,
	unavailable: [],
	adapter: base_url+'ajax_booking/get_property_availability_date_func?prop_id='+prop_id,
	onSelectDate: function(date, month, year)
	{
		//alert([year, month, date].join('-') + ' is: ' + this.isAvailable(date, month, year));
	}
  });
  
  
});
