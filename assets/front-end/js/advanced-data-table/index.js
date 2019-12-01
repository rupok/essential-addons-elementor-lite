var advanced_data_table_active_cell = null;

var Advanced_Data_Table = function($scope, $) {
	if (isEditMode) {
		var table = $scope.context.querySelector(".ea-advanced-data-table");

		// add edit class
		table.classList.add("ea-advanced-data-table-editable");

		// insert editable area
		table.querySelectorAll("th, td").forEach(function(el) {
			var value = el.innerHTML;

			if (value.indexOf('<textarea rows="1">') !== 0) {
				el.innerHTML = '<textarea rows="1">' + value + "</textarea>";
			}
		});
	}
};

// Inline edit
var Advanced_Data_Table_Inline_Edit = function(panel, model, view) {
	setTimeout(function() {
		if (view.el.querySelector(".ea-advanced-data-table")) {
			var interval;
			var table = view.el.querySelector(".ea-advanced-data-table");

			table.addEventListener("focusin", function(e) {
				if (e.target.tagName.toLowerCase() == "textarea") {
					advanced_data_table_active_cell = e.target;
				}
			});

			table.addEventListener("focusout", function(e) {
				if (e.target.tagName.toLowerCase() == "textarea") {
					clearTimeout(interval);

					// clone current table
					var origTable = table.cloneNode(true);

					// remove editable area
					origTable.querySelectorAll("th, td").forEach(function(el) {
						var value = el.querySelector("textarea").value;
						el.innerHTML = value;
					});

					// disable elementor remote server render
					model.remoteRender = false;

					// update backbone model
					model.setSetting("ea_adv_data_table_static_html", origTable.innerHTML);

					// enable elementor remote server render just after elementor throttle
					// ignore multiple assign
					interval = setTimeout(function() {
						model.remoteRender = true;
					}, 1001);
				}
			});
		}
	}, 300);
};

Advanced_Data_Table_Context_Menu = function(groups, element) {
	if (element.options.model.attributes.widgetType == "eael-advanced-data-table") {
		groups.push({
			name: "ea_advanced_data_table",
			actions: [
				{
					name: "add_row_above",
					title: "Add Row Above",
					callback: function() {
						var table = document.querySelector(".ea-advanced-data-table-" + element.options.model.attributes.id);

						if (advanced_data_table_active_cell !== null) {
							var index = advanced_data_table_active_cell.parentNode.parentNode.rowIndex;
							var row = table.insertRow(index);

							for (var i = 0; i < table.rows[0].cells.length; i++) {
								var cell = row.insertCell(i);
								cell.innerHTML = '<textarea rows="1"></textarea>';
							}

							advanced_data_table_active_cell = null;
						}
					}
				},
				{
					name: "add_row_below",
					title: "Add Row Below",
					callback: function() {
						var table = document.querySelector(".ea-advanced-data-table-" + element.options.model.attributes.id);

						if (advanced_data_table_active_cell !== null) {
							var index = advanced_data_table_active_cell.parentNode.parentNode.rowIndex + 1;
							var row = table.insertRow(index);

							for (var i = 0; i < table.rows[0].cells.length; i++) {
								var cell = row.insertCell(i);
								cell.innerHTML = '<textarea rows="1"></textarea>';
							}

							advanced_data_table_active_cell = null;
						}
					}
				},
				{
					name: "add_column_left",
					title: "Add Column Left",
					callback: function() {
						var table = document.querySelector(".ea-advanced-data-table-" + element.options.model.attributes.id);

						if (advanced_data_table_active_cell !== null) {
							var index = advanced_data_table_active_cell.parentNode.cellIndex;

							for (var i = 0; i < table.rows.length; i++) {
								var cell = table.rows[i].insertCell(index);
								cell.innerHTML = '<textarea rows="1"></textarea>';
							}

							advanced_data_table_active_cell = null;
						}
					}
				},
				{
					name: "add_column_right",
					title: "Add Column Right",
					callback: function() {
						var table = document.querySelector(".ea-advanced-data-table-" + element.options.model.attributes.id);

						if (advanced_data_table_active_cell !== null) {
							var index = advanced_data_table_active_cell.parentNode.cellIndex + 1;

							for (var i = 0; i < table.rows.length; i++) {
								var cell = table.rows[i].insertCell(index);
								cell.innerHTML = '<textarea rows="1"></textarea>';
							}

							advanced_data_table_active_cell = null;
						}
					}
				},
				{
					name: "delete_row",
					title: "Delete Row",
					callback: function() {
						var table = document.querySelector(".ea-advanced-data-table-" + element.options.model.attributes.id);

						if (advanced_data_table_active_cell !== null) {
							var index = advanced_data_table_active_cell.parentNode.parentNode.rowIndex;

							table.deleteRow(index);

							advanced_data_table_active_cell = null;
						}
					}
				},
				{
					name: "delete_column",
					title: "Delete Column",
					callback: function() {
						var table = document.querySelector(".ea-advanced-data-table-" + element.options.model.attributes.id);

						if (advanced_data_table_active_cell !== null) {
							var index = advanced_data_table_active_cell.parentNode.cellIndex;

							for (var i = 0; i < table.rows.length; i++) {
								table.rows[i].deleteCell(index);
							}

							advanced_data_table_active_cell = null;
						}
					}
				}
			]
		});
	}

	return groups;
};

jQuery(window).on("elementor/frontend/init", function() {
	if (isEditMode) {
		elementor.hooks.addFilter("elements/widget/contextMenuGroups", Advanced_Data_Table_Context_Menu);
		elementor.hooks.addAction("panel/open_editor/widget/eael-advanced-data-table", Advanced_Data_Table_Inline_Edit);
	}

	elementorFrontend.hooks.addAction("frontend/element_ready/eael-advanced-data-table.default", Advanced_Data_Table);
});
