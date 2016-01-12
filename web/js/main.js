$(function() {

  $('a[rel="external"]').attr('target', '_blank');

  if($('#main-photo').length > 0) {
    var img = $('#main-photo');
    main_img = { w: parseInt(img.width()),
                 h: parseInt(img.height()) };
    var resize_main = function() {
      var img_per = 0.95;
      var winX = parseInt(img.parent('article').width());
      var winY = parseInt(img.parent('article').height());

      var scale = img_per*winX/main_img.w;
  
      if(scale > 1)
        scale = 1;
  
      var newX = Math.floor(scale*main_img.w);
      var newY = Math.floor(scale*main_img.h);
      if(winX >= 0 && winY >= 0)
        img.width(newX).height(Math.floor(newY));
//      img.find('div').each(function(i) {
//        $(this).width(main_img.d[i].w*scale);
//        var fs_scale = 1.5*scale;
//        var lh_scale = 1.5*scale;
//        if(fs_scale >= 1)
//          fs_scale = 1;
//        if(lh_scale >= 1)
//          lh_scale = 1;
//        $(this).css('font-size', fs_scale*main_img.d[i].fs);
//        var px = lh_scale*main_img.d[i].lh + 'px';
//        $(this).css('line-height', px);
//      });
    }
    resize_main();
  
    var updatesize;
    $(window).resize(function(){
        clearTimeout(updatesize);
        updatesize = setTimeout(function() {resize_main();}, 100);
    });

  }

  $('form:first *:input[type!=hidden]:first').focus();


});
