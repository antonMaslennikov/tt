$('.subtasks').find('input[type=checkbox]').live('click', function(){

    var id = $(this).attr('_id');
	var ch = $(this).attr('checked');
	var i = this;
    
    $(this).attr('disabled', 'disabled');
    
    if (ch == 'checked')
    {
        $(this).parent().children('.finishTask-widget').toggle();
    } 
    else
    {
        $.get('index.php', {'r' : 'tasks/finish', 'id' : id}, function (r){
            $('#subtask-' + id + ' span.text').removeClass('finished');
            $(i).removeAttr('disabled');
        });
    }

});

/*
$('.finishTask-form').submit(function(){
	
	$.post($(this).attr('action'), $(this).serialize(), function (r){
	
		if (ch == 'checked')
			$(i).next('span.text').addClass('finished');
		else
			$(i).next('span.text').removeClass('finished');
			
		$(i).removeAttr('disabled');
	});

    return false;
});
*/

$('.finishTask-widget input[type=reset]').click(function(){
    
    $(this).parent().parent().hide();
    $(this).parent().parent().parent().find('input[type=checkbox]').removeAttr('checked').removeAttr('disabled');
    
});

$('#tasksFilters').find('select').change(function(){

	var url = $('#tasksFilters').find('input[name=url]').val();
	
	$('#tasksFilters').find('select').each(function(){
	
		if ($(this).val().length > 0)
		{
			url += '&' + $(this).attr('name') + '=' + $(this).val();
			$.cookie('tt_' + $(this).attr('name'), $(this).val());
		}
		else
		{
			$.cookie('tt_' + $(this).attr('name'), '');
		}
	});
	
	location.href=url;
});

/**
 * показать форму загрузки файла
 */
$('.showUploadForm').click(function(){

	$(this).hide();
	$(this).next().show();
	return false;
	
});

/**
 * показать форму добавления подзадачи
 */
$('.showSubtaskForm').click(function(){

	if ($(this).text() != $(this).attr('title'))
		$(this).text($(this).attr('title'));
	else
		$(this).text('Отменить');
		
	$(this).next().toggle();
	return false;
	
});

/**
 * показать форму выбора исполнителей
 */
$('.select-executors').click(function(){
	$(this).next().slideToggle('fast');
	return false;
});


/**
 * плавное затухание сообщения об ошибке
 */
$(".error-message").animate({opacity: 1.0}, 3000).fadeOut("slow");

/**
 * выбор исполнителя из списка
 */
/*
$('#executors-widget').find('input[type=checkbox]').change(function(){

	var container = $('#executors-widget').find('input[name=executors]');
	
	if (container.length == 0) {
		container = document.createElement('input');
		$(container).attr('name', 'executors').attr('type', 'hidden').appendTo('#executors-widget');
	}
	
	$(container).val('');
	
	$('#executors-widget').find('input[type=checkbox]:checked').each(function(){
		$(container).val($(container).val() + $(this).val() + ', ');
	})
	
	return false;

});

*/