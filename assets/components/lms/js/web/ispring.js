	var ispringPresentationConnector = {};
	ispringPresentationConnector.register = function(player) {
		
		playbackController = player.view().playbackController();

		initButtonsEventsHandlers();
		initPlaybackControllerEventsHandlers();

	}

	function initPlaybackControllerEventsHandlers() {
		
		playbackController.slideChangeEvent().addHandler(function(slideIndex) {
			var lastSlide = playbackController.lastSlideIndex();

			if (slideIndex > 0 && slideIndex != lastSlide) {
				var progress = slideIndex*100/playbackController.lastSlideIndex();
				if (progress > 0) {
				 	$.ajax({
						type: "POST"
		                ,url: LMSConfig.actionUrl
		                ,dataType: 'json'
						,data: {action: 'module/update_progress', progress: progress.toFixed()},//'action=update_progress&progress='+progress.toFixed(),
						success: function(response){
							var data = response.data;
							$('a[data-id="'+ data.parent + '"').parent()
							  .find('.progress').show()
							  .find('.progress-bar').attr('aria-valuenow', data.progress)
							  .css('width', data.progress+'%').text(data.progress+'%');
						}
					});
			 	}
			}
		});
		
		playbackController.playbackCompleteEvent().addHandler(function() {
			$.ajax({
				type: "POST"
		        ,url: LMSConfig.actionUrl
		        ,dataType: 'json'
				,data: {action: 'module/update_progress', progress: 100}, //'action=update_progress&progress=100',
				success: function(response){
					var data = response.data;
					$('a[data-id="'+ data.parent + '"').parent()
						.removeClass().addClass('yes');
					if (data.hasOwnProperty('tests')) {
						$('.tests').html(data.tests);
					}
				}
			});
		});
	}

	function initButtonsEventsHandlers() {
		$(".modal-fullscreen").on('hidden.bs.modal', function () {
			playbackController.pause();
		});
	}