$(document).ready(function () {
    $(".word-progress").knob();

    $("body").keydown(function(e) {
        if (e.which == 27) {
            var alreadyLearnedButton = $('[name=already_learned]');
            if(alreadyLearnedButton.length) {
                alreadyLearnedButton.click();
                return false;
            }
        }

        if (e.which == 112) {
            var hintButton = $('[name=hint]')
            if(hintButton.length) {
                hintButton.click();
                return false;
            }
        }
    });

    if($('#task-show').length) {
        $("body").keypress(function(e) {
            if (e.which == 13) {
                $('[name=next]').click();
                return false;
            }
        });
    }

    if($('#task-select').length) {
        $("body").keypress(function(e) {
            var keyCodeToButtonIndexMap = {49: 0, 50: 1, 51: 2, 52: 3, 53: 4};
            var buttonIndex = keyCodeToButtonIndexMap[e.which];
            if(buttonIndex !== undefined) {
                $('[name=answer]')[buttonIndex].click();
                return false;
            }
        });
    }

    if($('#task-mouse-type').length) {
        $("body").keypress(function(e) {
            var letter = String.fromCharCode(e.keyCode);
            letter = letter == ' ' ? '&nbsp;' : letter;

            var button = $('button[value="' + letter + '"]');

            if(button.length) {
                button.click();
            }
        });
    }

    if($('#task-keyboard-type').length) {
        $('[name=answer]').focus();

        $("body").keypress(function(e) {
            if (e.which == 13) {
                $('[name=check]').click();
                return false;
            }
        });
    }
});
