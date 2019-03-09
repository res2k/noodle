(function($){
    $.fn.disableSelection = function() {
        return this
        .attr('unselectable', 'on')
        .css('user-select', 'none')
        .css('-moz-user-select', 'none')
        .css('-khtml-user-select', 'none')
        .css('-webkit-user-select', 'none')
        .on('selectstart', false)
        .on('contextmenu', false)
        .on('keydown', false)
        .on('mousedown', false);
    };
 
    $.fn.enableSelection = function() {
        return this
        .attr('unselectable', '')
        .css('user-select', '')
        .css('-moz-user-select', '')
        .css('-khtml-user-select', '')
        .css('-webkit-user-select', '')
        .off('selectstart', false)
        .off('contextmenu', false)
        .off('keydown', false)
        .off('mousedown', false);
    };

    $.fn.rotateClass = function(classes) {
        // hack
        this.removeClass("maybe"); 
        for (var i = 0; i < classes.length; i++) {
            if (this.hasClass(classes[i])) {
                this.removeClass(classes[i]);
                i++;
                if (i >= classes.length) {
                    i = 0;
                }
                this.addClass(classes[i]);
                return(classes[i]);
            }
        }
        // none found so set the first class
        if (classes.length > 0) {
            this.addClass(classes[0]);
        }
        return(classes[0]);
    } 
})(jQuery);

$(document).ready(function(){
    function changeTextField(context, value)
    {
        context.each(function() {
            if (this.parentElement.MaterialTextfield)
            {
                this.parentElement.MaterialTextfield.change(value);
            }
        });
    }

    function userReset()
    {
        hideNameRequirement();

        $("table.dateSelect td").removeClass("yes no maybe");
        $("table.dateSelect th").removeClass("yes no maybe");

        $(".cobble").removeClass("yes no"); 
        $("#deleteUser").removeClass("activ");
        $("#changeName").removeClass("activ");
        $(".attend").removeClass("yes");
        $("input:checkbox").each(function() {
            // Looks odd, but method must be called on the label that become the MaterialCheckbox
            this.parentElement.MaterialCheckbox.uncheck();
        });
    }
    
    function displayAjaxError(jqXHR, textStatus, errorThrown)
    {
        var snackbar = document.querySelector('#noodle-toast');
        var data = {message: 'Fehler! Bitte später nochmal probieren.'};
        snackbar.MaterialSnackbar.showSnackbar(data);
    }
    
    function userUpdate(user)
    {
        $.ajax({
            url:'ajax.php?action=getAttendance',
            type:'POST',
            data:{user:user},
            dataType:"json",
            success:function(data, textStatus, jqXHR)
            {
                $("table.dateSelect td").removeClass("yes no maybe");
                $("table.dateSelect th").removeClass("yes no maybe");
                for ( did in data )
                {
                    if(did != '0000-00-00')
                        $("#"+did).attr("class",data[did]); 
                } 
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                displayAjaxError (jqXHR, textStatus, errorThrown);
            }
        });

        $.ajax({
            url:'ajax.php?action=getAttributes',
            type:'POST',
            data:{user:user},
            dataType:"json",
            success:function(data, textStatus, jqXHR)
            {
                for ( attr in data )
                {
                    // Looks odd, but method must be called on the label that become the MaterialCheckbox
                    if (data[attr] == 'yes')
                    {
                        document.querySelector("#attr_"+attr).MaterialCheckbox.check();
                    }
                    else
                    {
                        document.querySelector("#attr_"+attr).MaterialCheckbox.uncheck();
                    }
                } 
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                displayAjaxError (jqXHR, textStatus, errorThrown);
            }
        });

        $.ajax({
            url:'ajax.php?action=getAttend',
            type:'POST',
            data:{user:user},
            dataType:"json",
            success:function(data, textStatus, jqXHR)
            {
                for ( tid in data )
                {
                    $("th#tid-"+tid+" button").removeClass("yes"); 
                    $("th#tid-"+tid+" button").addClass(data[tid]); 
                } 
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                displayAjaxError (jqXHR, textStatus, errorThrown);
            }
        });


        $("#deleteUser").addClass("activ");      
        $("#changeName").addClass("activ");
        Cookies.set('username', user, { expires: 30 });
    }

    $("#deleteUser").click(function(){
        var user = $("#inputName").val();
       
        var check = confirm(unescape("Eigene Daten l%F6schen??"));
        if ( check == false )
        {
            return false;
        }
        $.ajax({
            url:'ajax.php?action=deleteUser',
            type:'POST',
            data:{user:user},
            dataType:"json",
            success:function(data, textStatus, jqXHR)
            {
                userReset();
                $("#inputName").val("");
                $(".training").each(function(){
                    var did = $(this).attr('id').substr(9);
                    getParticipants(did,'auto');
                });
                $("#deleteUser").removeClass("activ");
                $("#changeName").removeClass("activ");

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                displayAjaxError (jqXHR, textStatus, errorThrown);
            }
        });
        
    });

    var options = {
        serviceUrl: 'ajax.php?action=getNames',
        onSelect: function(value, data){
            userUpdate(value.value)
        },
        nocache: true,
    };
    $('#inputName').autocomplete(options);
    var k = 0;
    $("#inputName").keydown(function(e){
        if ( (e.keyCode < 48 || e.keyCode > 90) && e.keyCode != 46 && e.keyCode != 8 )
            return;
        
        userReset(); 
    });
    $("#inputName").on("input", function(e){
        userReset(); 
    });
    $("table.dateSelect").disableSelection(); 
    $(".cobble").disableSelection();
    $(".number_yes").disableSelection();
    $(".number_no").disableSelection();
    $("#help").disableSelection();

    // Hide drawer after click
    $(".mdl-navigation__link").click(function() { $("#drawer").removeClass("is-visible"); });
    
    window.addEventListener('load', function() {
        // Text field might be pre-filled by the browser
        if ( $("#inputName").val())
        {
            userUpdate($("#inputName").val());
        }
        else
        {
            var cookie_user = Cookies.get('username');
            if (cookie_user)
            {
                changeTextField($("#inputName"), cookie_user);
                userUpdate (cookie_user);
            }
        }
    });

    function attendanceUpdate(did, attend)
    {
        var user = $("#inputName").val();
        $.ajax({
            url:'ajax.php?action=attendanceUpdate',
            type:'POST',
            data:{did:did,attend:attend,user:user},
            dataType:"json",
            success:function(data, textStatus, jqXHR)
            {
                for ( did in data )
                {
                    getParticipants(did, 'auto');
                } 
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                displayAjaxError (jqXHR, textStatus, errorThrown);
            }
        });
        $("#deleteUser").addClass("activ");      
        $("#changeName").addClass("activ");
    }

    function attributeUpdate(attr, choice)
    {
        var user = $("#inputName").val();
        $.ajax({
            url:'ajax.php?action=attributeUpdate',
            type:'POST',
            data:{attr:attr,choice:choice,user:user},
            dataType:"json",
            success:function(data, textStatus, jqXHR)
            {
                $(".training").each(function(){
                    var did = $(this).attr('id').substr(9);
                    getParticipants(did,'auto');
                });
              // alert(data);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
//                alert("error: "+jQuery.parseJSON(jqXHR));
                  displayAjaxError (jqXHR, textStatus, errorThrown);
            }
        });
        $("#deleteUser").addClass("activ");      
        $("#changeName").addClass("activ");      

    }

    function attendUpdate (id, choice)
    {
        var user = $("#inputName").val();
        $.ajax({
            url:'ajax.php?action=attendUpdate',
            type:'POST',
            data:{tid:id,choice:choice,user:user},
            dataType:"json",
            success:function(data, textStatus, jqXHR)
            {
//                $(".training").each(function(){
//                    var day = $(this).attr('id').substr(9,10);
//                    getParticipants(day,'auto');
//                });
//              alert(data);
//                alert("success");
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
//                alert(textStatus);
//                alert("error: "+jQuery.parseJSON(jqXHR));
                  displayAjaxError (jqXHR, textStatus, errorThrown);
            }
        }); 

    }

    function getParticipants(did,expand,groups)
    {
        var context = $("#training_"+did);

        if(typeof groups === "undefined"){
            groups = ["yes","no"];
        }
        if (typeof groups == "string"){
            groups = [groups];
        }
        
        function showSpinner(spinner)
        {
            spinner.addClass("mdl-progress__indeterminate");
            spinner.addClass("is-active");
        }
        function hideSpinner(spinner)
        {
            spinner.removeClass("is-active");
            spinner.removeClass("mdl-progress__indeterminate");
        }

        if ( groups.indexOf("yes") >= 0)
        {
            showSpinner($("#spinner-yes-" + did, context));
        }

        if ( groups.indexOf("no") >= 0)
        {
            showSpinner($("#spinner-no-" + did, context));
        }


        if ( expand == 'auto' )
        { 
            expand = []; 
            for ( var i=0; i<groups.length; i++ )
            {
                expand[i] = ($(".number_"+groups[i],context).hasClass("expanded-yes") ? true : false); 
            }

        }

        if ( typeof expand == "boolean" )
        {
            expand = [expand];
        }
        
        // If contracting, do so immediately
        
        for ( var i=0; i<groups.length; i++ )
        {
            var g = groups[i];

            if (!expand[i])
            {
                $(".number_" + g, context).addClass("expanded-no");
                $(".number_" + g, context).removeClass("expanded-yes");
                $(".participants_" + g + " .participant",context).remove();
                $(".participants_" + g + " .participants-inner",context).removeClass("expanded");
            }
        }
         

        $.ajax({
            url:'ajax.php?action=getParticipants',
            type:'POST',
            data:{did:did},
            dataType:"json",
            success:function(data, textStatus, jqXHR)
            {
                for ( var i=0; i<groups.length; i++ )
                {
                    var g = groups[i];
                    
                    var text = "";
                    
                    if ( g == "yes" )
                    {

                        var minT = data['yes'];
                        var maxT = data['yes'] + data['maybe'];

                        text = (minT == maxT ? minT : minT + " bis " + maxT) + " Teilnehmer";
                    }
                    else if ( g == "no" )
                    {
                        text = data['no'] + " kommen nicht";
                    }
                    
                    $(".number_" + g + " .text", context).html(text);
                    
                    if ( expand[i] )
                    {
                        $(".number_" + g + "", context).addClass("expanded-yes");
                        $(".number_" + g + "", context).removeClass("expanded-no");
                        $(".participants_" + g + " .participant",context).remove();
                        $(".participants_" + g + " .participants-inner",context).addClass("expanded");

                        if ( data[g] > 0 )
                        {
                            for ( var key in data )
                            {
                                if ( key != 'yes' && key != 'maybe' && key != 'no' )
                                {
                                    if ( data[key]['type'] != g)
                                        continue;
                                    var text = data[key]['user'];
                                    if ( data[key]['attr'] && data[key]['attr'].length > 0 )
                                        text = text + ' (' + data[key]['attr'].join(', ') + ')';
                                    $(".participants_" + g + " .participants-list",context).append('<div class="participant '+data[key]['type']+'">'+ text + '</div>');
                                }
                            }
                        }
                    
                    }                        
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                  var show_error = false;
                  for ( var i=0; i<groups.length; i++ )
                  {
                      show_error = show_error || expand[i];
                  }
                  if (show_error)
                  {
                      displayAjaxError (jqXHR, textStatus, errorThrown);
                  }
                  // else: hide errors if collapsing
            }
        }).always(function() {
              for ( var i=0; i<groups.length; i++ )
              {
                  var g = groups[i];
                  hideSpinner($("#spinner-" + g + "-" + did, context));
              }
        });
    }

    function changeName ( oldName, newName, onSuccess, onError )
    {
        if ( oldName == newName )
            onSuccess();
        else {
            $.ajax({
                url:'ajax.php?action=changeName',
                type:'POST',
                data:{oldName:oldName,newName:newName},
                dataType:"json",
                success:function(data, textStatus, jqXHR){
                    if (data.success)
                        onSuccess();
                    else
                        onError();
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    displayAjaxError (jqXHR, textStatus, errorThrown);
                }
            });
        }
    }
    
    function showNameRequirement() {
        $("#inputName").siblings(".mdl-textfield__error").addClass("force-visible");
    }

    function hideNameRequirement() {
        $("#inputName").siblings(".mdl-textfield__error").removeClass("force-visible");
    }

    $("table.dateSelect td").click(function(event){
        if ( !$("#inputName").val())
        {
            showNameRequirement();
            return false;
        }

//        var classes = ["yes","maybe","no",""];

        var classes = ["yes","no",""];
        var col = $(this).parent().children().index($(this));

        $("table.dateSelect th").eq(col).removeClass(classes.join(" "));

        var newClass = $(this).rotateClass(classes);
        attendanceUpdate([$(this).attr('id')],[newClass]);
    });

    $("table.dateSelect .woy").unbind("click").click(function(){
        if ( !$("#inputName").val())
        {
            showNameRequirement();
            return false;
        }
//        var classes = ["yes","maybe","no",""];

        var classes = ["yes","no",""];
        var toAdd = "yes";
        var toRemove = classes.join(" "); 

        for ( var i=0; i < classes.length; i++ )
        {
            if ( $(this).hasClass(classes[i]))
            {
                var j = (i+1)%classes.length;

                toAdd = classes[j];
                i = classes.length;
            }
        } 
        var context = $(this).parent();
        var date = [];
        var attend = [];

        jQuery.each($("td",context), function(){
            $(this).removeClass(toRemove);
            $(this).addClass(toAdd);
            if ( $(this).attr('id'))
            {
                date.push($(this).attr('id'));
                attend.push(toAdd);
            }
        });
        attendanceUpdate(date,attend);
       
    });


    $("table.dateSelect button.attend").click(function(event){
        if ( !$("#inputName").val())
        {
            showNameRequirement();
            return false;
        }

        var col = $(this).parent().parent().children().index($(this).parent());
        var classes = ["yes",""];
        var remClasses = ["yes","maybe","no",""];
        var toAdd = "yes";
        var toRemove = remClasses.join(" "); 

        for ( var i=0; i < classes.length; i++ )
        {
            if ( $(this).hasClass(classes[i]))
            {
                var j = (i+1)%classes.length;

                toAdd = classes[j];
                i = classes.length;
            }
        } 

        var tid = $(this).attr('tid');
        $("th#tid-"+tid+" button").removeClass(toRemove); 
        $("th#tid-"+tid+" button").addClass(toAdd); 

        attendUpdate(tid, toAdd);

        var context = $(this).parent().parent().parent().parent();
        var date = [];
        var attend = [];

        jQuery.each($("tr",context), function(){
            if ( $(this).hasClass("first") == false)
            {
                $(this).children().eq(col).removeClass(toRemove);
                $(this).children().eq(col).addClass(toAdd);
                if ( $(this).children().eq(col).attr('id'))
                {
                    date.push($(this).children().eq(col).attr('id'));
                    attend.push(toAdd);
                }
            }
        });
        attendanceUpdate(date,attend);
    });

    $(".woyHeader").unbind("click");
    $(".noclick").unbind("click");
     
    $("div.userAttributes input").click(function(){
        if ( !$("#inputName").val())
        {
            showNameRequirement();
            return false;
        }
      
        var attr = ($(this).attr('attr_id'));
        var cl = (this.checked ? "yes" : "no");
        attributeUpdate(attr,cl);
    });

    $("div.participants div.number_yes").click(function(){
        var dayId = $(".training").has(this).attr('id');
        var did = dayId.substr(9);
        if ( $(this).hasClass("expanded-yes"))
        {
            getParticipants(did,false,"yes");
        }
        else
        {
            getParticipants(did,true,"yes");     
        }
    });

    $("div.participants div.number_no").click(function(){

        var dayId = $(".training").has(this).attr('id');
        var did = dayId.substr(9);
        if ( $(this).hasClass("expanded-yes"))
        {
            getParticipants(did,false,"no");
        }
        else
        {
            getParticipants(did,true,"no");     
        }
    });

    var renameDialog = document.querySelector('#renameDialog');
    if (!renameDialog.showModal) {
        dialogPolyfill.registerDialog(renameDialog);
    }

    function showDialogNameMessage(msg) {
        var error_span = $("#dlg_inputName").siblings(".mdl-textfield__error");
        error_span.addClass("force-visible");
        error_span.html(msg);
    }

    function hideDialogNameMessage() {
        $("#dlg_inputName").siblings(".mdl-textfield__error").removeClass("force-visible");
    }

    var oldName;
    $('#dlg_buttonChange', renameDialog).click (function() {
        var newName = $("#dlg_inputName").val();
        if ( newName == "" ){
            showDialogNameMessage("Bitte Namen eingeben");
        }
        else
        {
            function onSuccess (){
                $(".training").each(function(){
                    var did = $(this).attr('id').substr(9);
                    getParticipants(did,'auto');
                });
                changeTextField ($("#inputName"), newName);
                renameDialog.close();
            }
            function onError (){
                showDialogNameMessage("Name schon vorhanden");
            }
            changeName(oldName, newName, onSuccess, onError); 
        }
    });
    $('#dlg_buttonReset', renameDialog).click (function() {
        renameDialog.close();
    });

    $("#changeName").click(function(){
        oldName = $("#inputName").val();
        changeTextField ($("#dlg_inputName", renameDialog), oldName);
        hideDialogNameMessage();
        renameDialog.showModal();
    });
});
