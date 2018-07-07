/**
 *
 */
/*
 * // DOM Position key index //
 *
 * l - Length changing (dropdown) f - Filtering input (search) t - The Table!
 * (datatable) i - Information (records) p - Pagination (paging) r - pRocessing <
 * and > - div elements <"#id" and > - div with an id <"class" and > - div with
 * a class <"#id.class" and > - div with an id and class
 *
 * Also see: http://legacy.datatables.net/usage/features
 */



var breakpointDefinition = {
	tablet : 1024,
	phone : 480
};
/* COLUMN FILTER  */

function generate_uri(suburi){
	if( typeof suburi === 'undefined'){
		suburi = 'edit';
	}
	var url = '';
    var href = window.location.href;
    if( href.slice(-5)===".html"){
        url = href.substring(0,href.length - 5) + '/'+suburi+'/';
    }else if( href.slice(-4)===".htm"){
        url = href.substring(0,href.length - 4) + '/'+suburi+'/';
    } else {
        url = href + '/'+suburi+'/';
    }
    return url;
}

var tables = {
	url :null,
	columns :[],
	breakpointDefinition : { tablet : 1024, phone : 480 },
	area:null,
	table:undefined,
	helper:undefined,
	load:function(attribute){
	    tables.area = $(attribute);

        jQuery.each(tables.columns,function (index,setting) {

            if( typeof setting.render_img !== 'undefined' ) {
                tables.columns[index].render = function (data, type, full, meta) {
                    if( typeof data === 'undefined'){
                        return null;
                    }
                	let imgSrc = "";
                    data = data.toString().trim();
                    if( data.length > 0 ){
                        imgSrc = (data.substring(0, 4) === "http" ) ? data : setting.render_img + data;
					};

                    return '<img src="' + imgSrc + '" class="img-responsive" />';
                }
            }else if( typeof setting.link !== 'undefined' ){
                tables.columns[index].render = function (data) {
                    let out = "";
                    if( data != null && data.length > 0 ){
                        data.map((ite)=>{
                            out += '<a href="' + ite.link + '" target="_blank" >'+ite.label+'</a>';
                        });
					}

					return out;
                }
            } else if( typeof setting.status_label !== 'undefined' ){
                tables.columns[index].render = function (data, type, full, meta) {
                	let label = "<span class=\"btn btn-default disabled\"><i class=\"fa fa-eye-slash\"></i></span>";
                	if( data === 1 ){
                        label = "<span class=\"btn btn-primary btn-xs\"><i class=\"fa fa-gear fa-spin\"></i></span>";
					}
                    return label;
                }
            }

        });

        var tableAjax = false;
        if( tables.url !== null ){
            tableAjax = { url: tables.url, dataSrc: 'data' };
		}
	    tables.table  =  $(attribute).DataTable({
		    ajax: tableAjax,
            processing: true,
			serverSide: true,
            pageLength: 10,
			paging: true,

		    columns: tables.columns,

		    "sDom" : "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'l>r>"
				+ "t"
				+ "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",

		    "autoWidth" : true,


		    "preDrawCallback" : function() {
			if (!tables.helper) {
			    tables.helper = new ResponsiveDatatablesHelper( tables.area ,tables.breakpointDefinition);
			}
		    },
		    "rowCallback" : function(nRow) {
		    	tables.helper.createExpandIcon(nRow);
		    },
		    "drawCallback" : function(oSettings) {
		    	tables.helper.respond();
                var api = this.api();
                tables.row_ci_actions(api);
		    },
		    "initComplete": function () {


	        }
	    });
	    tables.columns_filter();

	},
	row_ci_actions:function (api) {
        api.$('button').click( function () {
            //api.search( this.innerHTML ).draw();
            var url = '';
            if( typeof $(this).attr('data-action') !== undefined ){
                if( $(this).attr('data-action')==='edit' ){
                    url = generate_uri('edit');

                    row_data = tables.table.row( $(this).parents('tr') ).data();
                    if( typeof(row_data) != undefined && typeof(row_data.id) != undefined ){
                        $(location).attr('href', url+ row_data.id);
                    }
                } else if ($(this).attr('data-action')==='delete'){
                    url = generate_uri('delete');
                    row_data = tables.table.row( $(this).parents('tr') ).data();
                    if( typeof(row_data) != undefined && typeof(row_data.id) != undefined ){
                        $(location).attr('href', url+ row_data.id);
                    }
                }
            }
        });

    },

	columns_filter:function(){
	    $("thead th input[type=text]",tables.area).on( 'keyup change', function () {
		tables.table
	            .column( $(this).parent().index()+':visible' )
	            .search( this.value )
	            .draw();

	    } );

	}
};
