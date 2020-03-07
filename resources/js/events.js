$(document).ready(() => {
	// Summernote default values
	$('#form-content').summernote({
		height: 150,
		focus: true,
	});
	
	// Dashboard settings menu size animation
	$('.settings-item').mouseenter(function() {
		$(this).addClass('active-hover');
	
		$(this).mouseleave(function() {
			$(this).removeClass('active-hover');
		});
	});
	
	// Password revealer button
	$('.password-revealer').click(function() {
		if ($(this).siblings('input').attr('type') === 'password') {
			$(this).siblings('input').attr('type', 'text');
			$(this).children('i').attr('class', 'far fa-eye-slash');
		} else {
			$(this).siblings('input').attr('type', 'password');
			$(this).children('i').attr('class', 'far fa-eye');
		}
	});
	
	// Error visuals when password_repeat doesn't match password
	$('#register_password_repeat').on('change', function() {
		if ($(this).val() !== $('#register_password').val()) {
			$(this).addClass('is-invalid');
			if (!$('#passwords-no-match').length) {
				$(this).parent().siblings('label').append(`
					<p class="color-red" id="passwords-no-match">Passwords don't match</p>
				`);
			}
		} else {
			$(this).removeClass('is-invalid');
			$('#passwords-no-match').remove();
		}
	});

	// Turn disabled off if all inputs are filled, otherwise turn it on
	$('.modal-auth input').on('input', function() {
		let modal = $(this).closest('form');
		let emptyFields = modal.find('input').not('[name=_token]').length;
		modal.find('input').not('[name=_token]').each(function() {
			if ($(this).val() !== '') emptyFields -= 1;
		});
		if (emptyFields === 0) {
			modal.find('[disabled]').removeAttr('disabled');
		} else {
			modal.find('[type=submit]').attr('disabled', true);
		}
	});
	
	// Open modals depending on which error element has been spawned
	if ($('#registerModal .is-invalid').length) {
		$('#registerModal').modal('show');
		$('.modal').on('shown.bs.modal', function() {
			$('#registerModal .is-invalid').first().focus();
		});
	} else if ($('#loginModal .is-invalid').length) {
		$('#loginModal').modal('show');
		$('.modal').on('shown.bs.modal', function() {
			$('#loginModal .is-invalid').first().focus();
		});
	} else if ($('#errorModal #error-any').length) {
		$('#errorModal').modal('show');
	} else {
		$('.modal').on('shown.bs.modal', function() {
			$(this).find('[autofocus]').focus();
		});
	}
	
	// Put spinning wheel on submits buttons when pressed
	if ($('.spin')) {
		$('.spin').click(function() {
			if (!$(this).hasClass('loading')) {
				$(this).addClass('loading').html('<i class="fas fa-circle-notch"></i>');
			}
		});
	}

	// Spawn scroll to top button
	$(document).on('scroll', function() {
		if ($(window).scrollTop() > $(window).height()) {
			if (!$('#scroller').hasClass('show')) {
				$('#scroller').addClass('show');
			}
		} else {
			if ($('#scroller').hasClass('show')) {
				$('#scroller').removeClass('show');
			}
		}
	});

	// Scroll to top
	$('#scroller').click(function() {
		window.scrollTo(0, 0);
	});

	// File upload button animation
	$('.file-upload').mouseenter(function() {
		$(this).children('i').attr('style', `transform: translateX(${$(this).width() / 2 - $(this).children('i').width() / 2}px);`);
		$(this).addClass('slide');

		$(this).mouseleave(function() {
			$(this).children('i').removeAttr('style');
			$(this).removeClass('slide');
		});
	});

	// Copy link instead of opening it, and spawn a small bubble notification
	$('.permalink').click(function(e) {
		e.preventDefault();

		// Remove all other currently displayed notifications before we start
		$('.copy-notification').each(function() {
			$(this).remove();
		});

		// Need some sort of text to copy
		$(this).append(`<textarea id="copy">${$(this).attr('href')}</textarea>`).attr('id', 'tooltip');

		// Copy the text and remove the dummy element
		$('#copy').select();
		document.execCommand('copy');
		$('#copy').remove();

		// Create the tooltip, and save its parent so we know which to remove automatically afterwards, in case user spawns multiple
		let tooltip = $(this).parent().append('<div class="copy-notification"><span>Copied!</span></div>');

		// Remove the targeted tooltip
		setTimeout(() => {
			tooltip.find('.copy-notification').remove();
		}, 2000);
	});

	// Spawn pagination input box
	$('.pagination .dots').click(function() {
		// Toggle input box when clicking on the triple dots, if the box already exists
		if ($('.pagination-go').length) {
			if ($('.pagination-go').hasClass('hide')) {
				$('.pagination-go').removeClass('hide');
			} else {
				$('.pagination-go').addClass('hide');
			}
		} else {
			// Add the input box if it doesn't exist
			$(this).parent().append(`
				<div class="pagination-go" id="pagination-go">
					<i class="fas fa-minus" id="pagination-minus"></i>
					<input type="tel" id="pagination-input" value="${$('.pagination').attr('data-current-page')}" />
					<i class="fas fa-plus" id="pagination-plus"></i>
					<a class="btn" href="#" id="pagination-submit">
						Go
					</a>
				</div>
			`);

			// Pagination input incrementers
			let interval = null;
			$('#pagination-minus').mousedown(function() {
				interval = setInterval(() => {
					if (Number($('#pagination-input').val()) > 1) {
						$('#pagination-input').val($('#pagination-input').val() - 1);
					} 
				}, 200);
			})
			.click(function() {
				if (Number($('#pagination-input').val()) > 1) {
					$('#pagination-input').val($('#pagination-input').val() - 1);
				}
			})
			.mouseup(function() {
				clearInterval(interval);
			});

			$('#pagination-plus').mousedown(function() {
				interval = setInterval(() => {
					if ($('#pagination-input').val() < Number($('.pagination .item-wrapper').last().prev().children('.item').html())) {
						$('#pagination-input').val(Number($('#pagination-input').val()) + 1);
					}
				}, 200);
			})
			.click(function() {
				if ($('#pagination-input').val() < Number($('.pagination .item-wrapper').last().prev().children('.item').html())) {
					$('#pagination-input').val(Number($('#pagination-input').val()) + 1);
				}
			})
			.mouseup(function() {
				clearInterval(interval);
			});

			// Autofocus when the input box spawns
			$('#pagination-input').focus();

			// Handle the submitted page dynamically and redirect to that page
			$('.pagination-go a').click(function() {
				window.location.href = `?page=${$('.pagination-go input').val()}`;
			});
		}

		// Put cursor at the end of the text
		document.getElementById('pagination-input').setSelectionRange(100, 100);
	});

	// Hide pagination input box when clicking outside of it or the triple dots
	$(window).click(function(e) {
		if (
			// Only hide it if user does not click on these elements, and if .pagination-go is not already hidden
			($(e.target).attr('id') !== 'pagination-submit') && 
			($(e.target).attr('id') !== 'pagination-input') && 
			($(e.target).attr('id') !== 'pagination-minus') && 
			($(e.target).attr('id') !== 'pagination-plus') && 
			($(e.target).attr('id') !== 'pagination-dots') && 
			($(e.target).attr('id') !== 'pagination-go') && 
			!$('.pagination-go').hasClass('hide')
		) {
			$('.pagination-go').addClass('hide');
		}
	});
});