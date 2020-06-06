
window.addEventListener("load", function()
{
    document.getElementById("nojs").style.display = "none";

    if (document.getElementById("complete"))
    {
        document.getElementById("complete").className = "fade";
    }

    if (document.getElementById("itemform"))
    {
        document.getElementById("search_item").style.display = "none";
    }

    if (document.getElementById("allitems"))
    {
        document.getElementById("search_item").addEventListener("keyup", searchTable);
        var oSortable = new SortableTable(document.getElementById("allitems"));
        document.getElementById("search_item").focus();
    }

    if ( ! document.getElementById("deletebutton") && document.getElementById("item")) // on 'new' page focus on first name field
    {
        document.getElementById("item").focus();
    }
    else
    {
        document.getElementById("search_item").focus();
    }

    if (document.getElementById("itemform"))
    {
        document.getElementById("itemform").addEventListener("submit", function(e)
        {
            var
                aFormFields = ["item", "expiry"];
                i = -1,
                n = aFormFields.length,
                oSubErrors = document.getElementById("submissionerrors"),
                sErrorCol = "#f2c9c9";

            for (; ++i < n;)
            {
                document.getElementById(aFormFields[i]).style.background = "#fff";
            }

            if ( ! /[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}/.test(document.getElementById("expiry").value))
            {
                oSubErrors.innerHTML = "Please enter a valid date to expiry.";
                document.getElementById("expiry").style.background = sErrorCol;
                document.getElementById("expiry").focus();
                e.preventDefault();
            }

            for (i = -1; ++i < n;)
            {
                if (document.getElementById(aFormFields[i]).value === "")
                {
                    oSubErrors.innerHTML = "Please enter an " + aFormFields[i] + ".";
                    document.getElementById(aFormFields[i]).style.background = sErrorCol;
                    document.getElementById(aFormFields[i]).focus();
                    e.preventDefault();
                    break;
                }
            }

        }, false);
    }


    if (document.getElementById("deletebutton"))
    {
        document.getElementById("deletebutton").addEventListener("click", function(e)
        {
            if ( ! window.confirm("Are you sure you wish to delete this item?!"))
            {
                if (e.preventDefault)
                {
                    e.preventDefault();
                }
                else
                {
                    e.returnValue = false;
                }
            }

        }, false);
    }

}, false);


function searchTable()
{
    /**
        * Heavily modified from a W3Schools example.
        * 28/09/2016
    */

    var
        input = document.getElementById("search_item"),
        filter = input.value.toUpperCase(),
        table = document.getElementById("allitems"),
        tr = table.getElementsByTagName("tr"),
        n = tr.length,
        td,
        td2,
        i = -1;

    for (; ++i < n;)
    {
        td = tr[i].getElementsByTagName("td")[0];
        td2 = tr[i].getElementsByTagName("td")[1];

        if (td || td2) // blocks <tr> in <th>
        {
            if (td.innerHTML.toUpperCase().indexOf(filter) > -1 || td2.innerHTML.toUpperCase().indexOf(filter) > -1)
            {
                tr[i].style.display = "";
            }
            else
            {
                tr[i].style.display = "none";
            }

        }
    }
}


function SortableTable (tableEl)
{
    /**
        * Sortable HTML table
        * www.webtoolkit.info
        * Refactored 28/09/2016
    */

    this.tbody = tableEl.getElementsByTagName("tbody");
    this.thead = tableEl.getElementsByTagName("thead");
    this.tfoot = tableEl.getElementsByTagName("tfoot");

    var
        thisObject = this,
        sortSection = this.thead,
        sortRow,
        n = 0,
        i = -1;

    // constructor actions
    if (!(this.tbody && this.tbody[0].rows && this.tbody[0].rows.length > 0))
    {
        return;
    }

    if (sortSection && sortSection[0].rows && sortSection[0].rows.length > 0)
    {
        sortRow = sortSection[0].rows[0];
    }
    else
    {
        return;
    }

    n = sortRow.cells.length;

    for (; ++i < n;)
    {
        sortRow.cells[i].sTable = this;
        sortRow.cells[i].onclick = function ()
        {
            this.sTable.sort(this);
            return false;
        }
    }

    this.getInnerText = function(el)
    {
        if (typeof(el.textContent) !== "undefined") {return el.textContent;}
        if (typeof(el.innerText) !== "undefined") {return el.innerText;}
        if (typeof(el.innerHTML) === "string") {return el.innerHTML.replace(/<[^<>]+>/g, "");}
    }

    this.getParent = function(el, pTagName)
    {
        if (el == null) {return null;}
        else if (el.nodeType == 1 && el.tagName.toLowerCase() == pTagName.toLowerCase())
        {
            return el;
        }
        else
        {
            return this.getParent(el.parentNode, pTagName);
        }
    }

    this.sort = function(cell)
    {
        var
            column = cell.cellIndex,
            itm = this.getInnerText(this.tbody[0].rows[1].cells[column]),
            sortfn = this.sortCaseInsensitive,
            newRows = [],
            i = -1,
            j = -1,
            n = this.tbody[0].rows.length;

        if (itm.match(/\d\d[-]+\d\d[-]+\d\d\d\d/)) {sortfn = this.sortDate;} // mm-dd-yyyy
        if (itm.replace(/^\s+|\s+$/g,"").match(/^[\d\.]+$/)) {sortfn = this.sortNumeric;}

        this.sortColumnIndex = column;

        for (; ++j < n;)
        {
            newRows[j] = this.tbody[0].rows[j];
        }

        newRows.sort(sortfn);

        if (cell.getAttribute("sortdir") == "down")
        {
            newRows.reverse();
            cell.setAttribute("sortdir", "up");
        }
        else
        {
            cell.setAttribute("sortdir", "down");
        }

        n = newRows.length;

        for (; ++i < n;)
        {
            this.tbody[0].appendChild(newRows[i]);
        }
    }

    this.sortCaseInsensitive = function(a, b)
    {
        var
            aa = thisObject.getInnerText(a.cells[thisObject.sortColumnIndex]).toLowerCase(),
            bb = thisObject.getInnerText(b.cells[thisObject.sortColumnIndex]).toLowerCase();

        if (aa == bb) return 0;
        if (aa < bb) return -1;
        return 1;
    }

    this.sortDate = function(a, b)
    {
        var
            aa = thisObject.getInnerText(a.cells[thisObject.sortColumnIndex]),
            bb = thisObject.getInnerText(b.cells[thisObject.sortColumnIndex]);

        date1 = aa.substr(6, 4) + aa.substr(3, 2) + aa.substr(0, 2);
        date2 = bb.substr(6, 4) + bb.substr(3, 2) + bb.substr(0, 2);

        if (date1 == date2) {return 0;}
        if (date1 < date2) {return -1;}
        return 1;
    }

    this.sortNumeric = function(a, b)
    {
        var
            aa = parseFloat(thisObject.getInnerText(a.cells[thisObject.sortColumnIndex])),
            bb = parseFloat(thisObject.getInnerText(b.cells[thisObject.sortColumnIndex]));

        if (isNaN(aa)) {aa = 0;}
        if (isNaN(bb)) {bb = 0;}
        return aa - bb;
    }
}
