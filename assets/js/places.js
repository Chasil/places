jQuery(function ($) {
	const $list = $('#pm-list');
	const $empty = $('#pm-empty');
	const $tableBody = $('#pm-table-body');
	const $filters = $('#pm-filters');
	const $loadMore = $('#pm-load-more');


	let currentPage = 1;
	let maxPage = 1;


	function ajax(action, data) {
		return $.post(PM_DATA.ajax_url, Object.assign({ action, nonce: PM_DATA.nonce }, data || {}), null, 'json');
	}


	function placeItemRow(item) {
		return (
			'<div class="border rounded p-3">' +
			'<div class="fw-bold">' + escapeHtml(item.name || '') + '</div>' +
			'<div class="text-muted small">' + escapeHtml(item.address || '') + '</div>' +
			'<div class="small">NIP: ' + escapeHtml(item.nip || '') + ' • REGON: ' + escapeHtml(item.regon || '') + '</div>' +
			'</div>'
		);
	}



	function tableRow(item) {
		return (
			'<tr data-id="' + item.id + '">' +
			'<td>' + escapeHtml(item.name || '') + '</td>' +
			'<td>' + escapeHtml(item.address || '') + '</td>' +
			'<td>' + escapeHtml(item.nip || '') + '</td>' +
			'<td>' + escapeHtml(item.regon || '') + '</td>' +
			'<td class="text-end">' +
			'<button class="btn btn-sm btn-outline-primary pm-edit" data-id="' + item.id + '">' + PM_DATA.i18n.edit + '</button>' +
			'</td>' +
			'</tr>'
		);
	}


	function escapeHtml(s) {
		return $('<div/>').text(s || '').html();
	}


	function getFilterData() {
		return {
			name: $('#pm-f-name').val(),
			address: $('#pm-f-address').val(),
			nip: $('#pm-f-nip').val(),
			regon: $('#pm-f-regon').val(),
		};
	}

	function load(page = 1, append = false) {
		const data = Object.assign({ page }, getFilterData());
		ajax('pm_get_places', data).done(function (res) {
			if (!res.success) return;
			const items = res.data.items || [];
			maxPage = res.data.max_page || 1;
			currentPage = res.data.page || 1;


			if (!append) {
				$list.empty();
				$tableBody.empty();
			}


			if (items.length === 0 && !append) {
				$empty.text('No results.');
			} else {
				$empty.text('');
			}


			items.forEach(function (item) {
				$list.append(placeItemRow(item));
				$tableBody.append(tableRow(item));
			});


			toggleLoadMore();
		});
	}


	function toggleLoadMore() {
		if (currentPage >= maxPage) {
			$loadMore.prop('disabled', true);
		} else {
			$loadMore.prop('disabled', false);
		}
	}

	// Initial load
	load(1, false);


// Filters (keyup with debounce)
	let debounceTimer;
	$filters.on('input', 'input', function () {
		clearTimeout(debounceTimer);
		debounceTimer = setTimeout(function () {
			currentPage = 1;
			load(1, false);
		}, 300);
	});


// Load More
	$loadMore.on('click', function () {
		if (currentPage < maxPage) {
			const nextPage = currentPage + 1;
			ajax('pm_get_places', Object.assign({ page: nextPage }, getFilterData())).done(function (res) {
				if (!res.success) return;
				const items = res.data.items || [];
				currentPage = res.data.page || nextPage;
				maxPage = res.data.max_page || maxPage;
				items.forEach(function (item) {
					$list.append(placeItemRow(item));
					$tableBody.append(tableRow(item));
				});
				toggleLoadMore();
			});
		}
	});

	// Add form
	$('#pm-add-form').on('submit', function (e) {
		e.preventDefault();
		const $btn = $(this).find('button[type="submit"]');
		const payload = {
			name: $('#pm-name').val(),
			address: $('#pm-address').val(),
			nip: $('#pm-nip').val(),
			regon: $('#pm-regon').val(),
		};
		$btn.prop('disabled', true).text(PM_DATA.i18n.saving);
		ajax('pm_add_place', payload).always(function(){
			$btn.prop('disabled', false).text(PM_DATA.i18n.add);
		}).done(function(res){
			if (res.success) {
				$('#pm-add-msg').html('<div class="alert alert-success">' + PM_DATA.i18n.added + '</div>');
				currentPage = 1;
				load(1, false);
				$('#pm-add-form')[0].reset();
			} else {
				$('#pm-add-msg').html('<div class="alert alert-danger">' + (res.data && res.data.message ? res.data.message : PM_DATA.i18n.error) + '</div>');
			}
		});
	});

	// Edit buttons -> open modal
	$(document).on('click', '.pm-edit', function(){
		const id = $(this).data('id');
		const $row = $('tr[data-id="' + id + '"]');
		$('#pm-edit-id').val(id);
		$('#pm-edit-name').val($row.find('td').eq(0).text());
		$('#pm-edit-address').val($row.find('td').eq(1).text());
		$('#pm-edit-nip').val($row.find('td').eq(2).text());
		$('#pm-edit-regon').val($row.find('td').eq(3).text());


		const modal = new bootstrap.Modal(document.getElementById('pmEditModal'));
		modal.show();
	});


// Save edit
	$('#pm-edit-save').on('click', function(){
		const payload = {
			id: $('#pm-edit-id').val(),
			name: $('#pm-edit-name').val(),
			address: $('#pm-edit-address').val(),
			nip: $('#pm-edit-nip').val(),
			regon: $('#pm-edit-regon').val(),
		};
		const $btn = $(this).prop('disabled', true).text(PM_DATA.i18n.saving);
		ajax('pm_update_place', payload).always(function(){
			$btn.prop('disabled', false).text(PM_DATA.i18n.save);
		}).done(function(res){
			if (res.success) {
				$('#pm-edit-msg').html('<div class="alert alert-success">' + PM_DATA.i18n.updated + '</div>');
// Update table row in place
				const id = payload.id;
				const $row = $('tr[data-id="' + id + '"]');
				$row.find('td').eq(0).text(payload.name);
				$row.find('td').eq(1).text(payload.address);
				$row.find('td').eq(2).text(payload.nip);
				$row.find('td').eq(3).text(payload.regon);
// Also refresh cards minimalistically: reload first page to stay consistent
				currentPage = 1; load(1, false);
				setTimeout(function(){ bootstrap.Modal.getInstance(document.getElementById('pmEditModal')).hide(); }, 500);
			} else {
				$('#pm-edit-msg').html('<div class="alert alert-danger">' + (res.data && res.data.message ? res.data.message : PM_DATA.i18n.error) + '</div>');
			}
		});
	});
});