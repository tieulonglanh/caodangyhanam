    $(function(){
        $('div.user').bt({
            contentSelector: "$(this).find('.user-tooltip .tooltip')",
            fill: '#FFF',
            cornerRadius: 4,
            strokeWidth: 0,
            shadow: true,
            shadowOffsetX: 3,
            shadowOffsetY: 3,
            shadowBlur: 8,
            shadowColor: '#a89f98',
            shadowOverlap: false,
            noShadowOpts: {strokeStyle: '#999', strokeWidth: 2},
            positions: ['top'],
            showTip: function(box){
                $(box).fadeIn('normal');
            },
            hideTip: function(box, callback){
                if (! $(box).is(':hover')) {
                    $(box).fadeOut('normal');
                }
                else
                {
                    $(box).mouseleave(function(){
                        $(box).fadeOut('normal', function(){
                            $(box).remove();
                        });
                    });
                }
            },
            spikeLength: 5,
            spikeGirth: 15
        });
    });