 // Начальная анимация первого набора фоточек
        function startAnim(){
            jQuery(".slider_list li:eq(0)").addClass("active");
            jQuery(".btn_more").attr('href', jQuery(".slider_list li a:eq(0)").attr('rel'));
            // Плавно показываем первое фото из набора
            jQuery(".photoset:eq(0) img:eq(0)").fadeIn(500, function(){
                // Плавно показываем второе фото из набора
                jQuery(".photoset:eq(0) img:eq(1)").fadeIn(3000, function(){
                    // Не менее плавно показываем третье фото
                    jQuery(".photoset:eq(0) img:eq(2)").fadeIn(3000);
                });
            });
        }

        // Анимация по клику на ссылочках
        function listClick(elemNum, lnk){
            jQuery(".slider_list li").removeClass("active");
            jQuery(".slider_list li:eq("+elemNum+")").addClass("active");
            jQuery(".photoset:visible").fadeOut(500, function(){
                jQuery(".photoset img").hide();
                jQuery(".photoset:eq("+elemNum+")").show();
                // Плавно показываем первое фото из набора
                jQuery(".photoset:eq("+elemNum+") img:eq(0)").fadeIn(500, function(){
                    // Плавно показываем второе фото из набора
                    jQuery(".photoset:eq("+elemNum+") img:eq(1)").fadeIn(3000, function(){
                        // Не менее плавно показываем третье фото
                        jQuery(".photoset:eq("+elemNum+") img:eq(2)").fadeIn(3000);
                    });
                });
                jQuery(".btn_more").attr('href', lnk);
            });

        }

        // Анимация по клику на стрелку Вправо
        function nextClick(){
            var slideElemCount = parseInt(jQuery(".photoset").size()) - 1;
            var currActive = jQuery(".photoset:visible").index();
            var nextActive = currActive+1;
            if (currActive >= slideElemCount){
                listClick(0,jQuery(".slider_list li:eq(0) a").attr("rel"));
            }
            else
            {
                listClick(nextActive,jQuery(".slider_list li:eq("+nextActive+") a").attr("rel"));
            }
        }

        // Анимация по клику на стрелку Влево
        function prevClick(){
            var slideElemCount = parseInt(jQuery(".photoset").size()) - 1;
            var currActive = jQuery(".photoset:visible").index();
            var prevActive = currActive-1;
            if (currActive > 0){
                listClick(prevActive,jQuery(".slider_list li:eq("+prevActive+") a").attr("rel"));
            }
            else
            {
                listClick(slideElemCount,jQuery(".slider_list li:eq("+slideElemCount+") a").attr("rel"));
            }
        }

        jQuery(document).ready(function() {
            var i = 0;
            jQuery(".slider_list li").each(function(){
                jQuery(this).attr('num',i);
                i++;
            });
            jQuery(".slider_list li a").click(function () {
                listClick(jQuery(this).parents("li").attr('num'), jQuery(this).attr("rel"));
          });

            jQuery(".btn_nav_r").click(function () {
                nextClick();
          });
            jQuery(".btn_nav_l").click(function () {
                prevClick();
          });
            startAnim();
        });