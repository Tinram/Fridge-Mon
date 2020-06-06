/*
	Tigra Calendar v4.0.4 (10/23/2009)
	http://www.softcomplex.com/products/tigra_calendar/
	Public Domain Software
	MSL: it has a few issues, but this is wonderfully lightweight and elegant.
	Blank field bug fixed 20/03/2012
*/

var aTCALparams = {
	"months" : ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
	"weekdays" : ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
	"yearscroll": true, // show year scroller
	"weekstart": 0, // first day of week: 0-Su or 1-Mo
	"centyear" : 70, // 2 digit years less than "centyear" are in 20xx, othewise in 19xx.
	"imgpath" : "images/calendar/"
};

var	aTCALS = [];
var aTCALSIDX = [];


function tcal(a_cfg) {

	var a_tpl = aTCALparams;

	// register in global collections
	this.s_id = a_cfg.id ? a_cfg.id : aTCALS.length;
	aTCALS[this.s_id] = this;
	aTCALSIDX[aTCALSIDX.length] = this;

	// assign methods
	this.f_show = f_tcalShow;
	this.f_hide = f_tcalHide;
	this.f_toggle = f_tcalToggle;
	this.f_update = f_tcalUpdate;
	this.f_relDate = f_tcalRelDate;
	this.f_parseDate = f_tcalParseDate;
	this.f_generDate = f_tcalGenerDate;

	// create calendar icon
	this.s_iconId = "tcalico_" + this.s_id;
	this.e_icon = document.getElementById(this.s_iconId);

	if (!this.e_icon) {
		document.write('<img src="' + a_tpl.imgpath + 'cal.gif" id="' + this.s_iconId + '" onclick="aTCALS[' + this.s_id + '].f_toggle()" class="tcalIcon" title="open calendar">');
		this.e_icon = document.getElementById(this.s_iconId);
	}

	// save received parameters
	this.a_cfg = a_cfg;
	this.a_tpl = a_tpl;
}


function f_tcalParseDate (s_date) {

	var oDate = new Date();
	var n_year = oDate.getYear();
	var n_month = oDate.getMonth();
	var n_day = aTCALparams.weekdays[parseInt(new Date().getDay(), 10)];
	return new Date(n_year, n_month, n_day);
}


function f_tcalGenerDate(d_date) {

	return (d_date.getFullYear() + "-" + (d_date.getMonth() < 9 ? "0" : "") + (d_date.getMonth() + 1) + "-" + (d_date.getDate() < 10 ? "0" : "") + d_date.getDate());
}


function f_tcalShow (d_date) {

	// find input field
	if (!this.a_cfg.controlname) {throw("TC: control name is not specified");}
	if (this.a_cfg.formname) {
		var e_form = document.getElementById(this.a_cfg.formname);
		if (!e_form) {throw("TC: form '" + this.a_cfg.formname + "' can not be found");}
		this.e_input = document.getElementById(this.a_cfg.controlname);
	}
	else {
		this.e_input = document.getElementById(this.a_cfg.controlname);
	}

	if (!this.e_input || !this.e_input.tagName || this.e_input.tagName !== "INPUT") {
		throw("TC: element '" + this.a_cfg.controlname + "' does not exist in " + (this.a_cfg.formname ? "form '" + this.a_cfg.controlname + "'" : 'this document'));
	}

	// dynamically create HTML elements if needed
	this.e_div = document.getElementById("tcal");

	if (!this.e_div) {
		this.e_div = document.createElement("DIV");
		this.e_div.id = "tcal";
		document.body.appendChild(this.e_div);
	}

	// hide all calendars
	f_tcalHideAll();

	// generate HTML and show calendar
	this.e_icon = document.getElementById(this.s_iconId);
	if (!this.f_update()) {return;}

	this.e_div.style.visibility = "visible";

	// change icon and status
	this.e_icon.src = this.a_tpl.imgpath + "no_cal.gif";
	this.e_icon.title = "close calendar";
	this.b_visible = true;
}


function f_tcalHide(n_date) {

	if (!this.b_visible) {return;} // no action if not visible

	if (n_date) {
		this.e_input.value = this.f_generDate(new Date(n_date));
	}

	this.e_div.style.visibility = "hidden";

	// change icon and status
	this.e_icon = document.getElementById(this.s_iconId);
	this.e_icon.src = this.a_tpl.imgpath + "cal.gif";
	this.e_icon.title = "open calendar";
	this.b_visible = false;
}


function f_tcalToggle() {
	return this.b_visible ? this.f_hide() : this.f_show();
}


function f_tcalUpdate(d_date) {

	var d_today = this.a_cfg.today? this.f_parseDate(this.a_cfg.today) : f_tcalResetTime(new Date());

	var d_selected =  (this.a_cfg.selected? this.f_parseDate(this.a_cfg.selected) : d_today);

	// date to display
	if (!d_date) { // selected by default
		d_date = d_selected;
	}
	else if (typeof(d_date) === "number") {
		d_date = f_tcalResetTime(new Date(d_date));
	}
	else if (typeof(d_date) === "string") {
		d_date = this.f_parseDate(d_date);
	}

	if (!d_date) {return false;}

	// first date to display
	var d_firstday = new Date(d_date);
	d_firstday.setDate(1);
	d_firstday.setDate(1 - (7 + d_firstday.getDay() - this.a_tpl.weekstart) % 7);

	var a_class, s_html = '<table class="ctrl"><tbody><tr>' + (this.a_tpl.yearscroll ? '<td' + this.f_relDate(d_date, -1, 'y') + ' title="Previous Year"><img src="' + this.a_tpl.imgpath + 'prev_year.gif" /></td>' : '') + '<td' + this.f_relDate(d_date, -1) + ' title="Previous Month"><img src="' + this.a_tpl.imgpath + 'prev_mon.gif" /></td><th>' + this.a_tpl.months[d_date.getMonth()] + ' ' + d_date.getFullYear() + '</th><td' + this.f_relDate(d_date, 1) + ' title="Next Month"><img src="' + this.a_tpl.imgpath + 'next_mon.gif" /></td>' + (this.a_tpl.yearscroll ? '<td' + this.f_relDate(d_date, 1, 'y') + ' title="Next Year"><img src="' + this.a_tpl.imgpath + 'next_year.gif" /></td></td>' : '') + '</tr></tbody></table><table><tbody><tr class="wd">';

	// print weekdays titles
	for (var i = 0; i < 7; i++) {
		s_html += "<th>" + this.a_tpl.weekdays[(this.a_tpl.weekstart + i) % 7] + "</th>";
	}
	s_html += "</tr>";

	// print calendar table
	var n_date, n_month, d_current = new Date(d_firstday);

	while (d_current.getMonth() === d_date.getMonth() || d_current.getMonth() === d_firstday.getMonth()) {
		s_html += "<tr>";

		for (var n_wday = 0; n_wday < 7; n_wday++) {

			a_class = [];
			n_date = d_current.getDate();
			n_month = d_current.getMonth();

			if (d_current.getMonth() !== d_date.getMonth()) { // other month
				a_class[a_class.length] = "othermonth";
			}
			if (d_current.getDay() === 0 || d_current.getDay() === 6) { // weekend
				a_class[a_class.length] = "weekend";
			}
			if (d_current.valueOf() === d_today.valueOf()) { // today
				a_class[a_class.length] = "today";
			}
			if (d_current.valueOf() === d_selected.valueOf()) { // selected
				a_class[a_class.length] = "selected";
			}

			s_html += '<td onclick="aTCALS[\'' + this.s_id + '\'].f_hide(' + d_current.valueOf() + ')"' + (a_class.length ? ' class="' + a_class.join(' ') + '">' : '>') + n_date + '</td>';
			d_current.setDate(++n_date);
		}
		s_html += "</tr>";
	}
	s_html += "</tbody></table>";

	// update HTML, positions and sizes
	this.e_div.innerHTML = s_html;

	var n_width = this.e_div.offsetWidth;
	var n_height = this.e_div.offsetHeight;
	var n_top = f_getPosition (this.e_icon, 'Top') + this.e_icon.offsetHeight;
	var n_left = f_getPosition (this.e_icon, 'Left') - n_width + this.e_icon.offsetWidth;
	if (n_left < 0) {n_left = 0;}
	this.e_div.style.left = n_left + 'px';
	this.e_div.style.top = n_top + 'px';

	return true;
}


function f_getPosition(e_elemRef, s_coord) {

	var n_pos = 0, n_offset, e_elem = e_elemRef;

	while (e_elem) {
		n_offset = e_elem["offset" + s_coord];
		n_pos += n_offset;
		e_elem = e_elem.offsetParent;
	}

	e_elem = e_elemRef;

	while (e_elem != document.body) {
		n_offset = e_elem["scroll" + s_coord];
		if (n_offset && e_elem.style.overflow === "scroll") {n_pos -= n_offset;}
		e_elem = e_elem.parentNode;
	}
	return n_pos;
}


function f_tcalRelDate(d_date, d_diff, s_units) {
	var s_units2 = (s_units === "y" ? "FullYear" : "Month");
	var d_result = new Date(d_date);
	d_result["set" + s_units2](d_date['get' + s_units2]() + d_diff);
	if (d_result.getDate() != d_date.getDate()) {d_result.setDate(0);}
	return ' onclick="aTCALS[\'' + this.s_id + '\'].f_update(' + d_result.valueOf() + ')"';
}


function f_tcalHideAll() {
	if (aTCALSIDX) {return;}
	for (var i = 0; i < aTCALSIDX.length; i++) {
		aTCALSIDX[i].f_hide();
	}
}


function f_tcalResetTime(d_date) {
	d_date.setHours(12);
	d_date.setMinutes(0);
	d_date.setSeconds(0);
	d_date.setMilliseconds(0);
	return d_date;
}
