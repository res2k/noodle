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
})(jQuery);


$(document).ready(function(){
    // Admin
    function changeTextField(context, value)
    {
        context.each(function() {
            if (this.parentElement.MaterialTextfield)
            {
                this.parentElement.MaterialTextfield.change(value);
            }
        });
    }

    function deleteSemester ( ID )
    {
        $.ajax({
            url:'ajax.php?action=deleteSemester',
            type:'POST',
            data:{ID:ID},
            dataType:"json",
            success:function(data, textStatus, jqXHR)
            {
                // Not elegant. But robust.
                location.href = "?q=admin";
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
//                alert("error: "+jQuery.parseJSON(jqXHR));
            }
        });
    }

    function startSemesterEditing(ID) {
         var row = $("#semester_" + ID);
         row.removeClass("normalMode");
         row.addClass("waitMode");

         $.ajax({
            url:'ajax.php?action=loadSemester',
            type:'POST',
            data:{ID:ID},
            dataType:"json",
            success:function(data, textStatus, jqXHR)
            {
                row.removeClass("waitMode");
                row.addClass("editMode");

                changeTextField($("#inputSemesterTitle", row), data.title);
                changeTextField($("#inputSemesterFrom", row), data.fromGer);
                changeTextField($("#inputSemesterTo", row), data.toGer);
                $(".submit", row).unbind("click").click(function(){
                    editSemester(ID);
                });

                $(".reset", row).unbind("click").click(function(){
                    cancelSemesterEditing(ID);
                });
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
            }
         });
    }

    function cancelSemesterEditing(ID) {
         var row = $("#semester_" + ID);
         row.removeClass("waitMode");
         row.removeClass("editMode");
         row.addClass("normalMode");
    }

    function editSemester(ID){
        var row = $("#semester_" + ID);
        $.ajax({
            url:'ajax.php?action=editSemester',
            type:'POST',
            data:{
                ID:ID,
                title:$("#inputSemesterTitle", row).val(),
                from:$("#inputSemesterFrom", row).val(),
                to:$("#inputSemesterTo", row).val(),
            },
            dataType:"json",
            success:function(data, textStatus, jqXHR)
            {
                if ( data.error )
                {
                    alert(data.error);
                }
                else {
                    // Not elegant. But robust.
                    location.href = "?q=admin";
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
//                alert("error: "+jQuery.parseJSON(jqXHR));
            }
        });  
    }
    
    function addSemester()
    {
         $.ajax({
            url:'ajax.php?action=addSemester',
            type:'POST',
            data:{
                title:$(".semester_new #inputSemesterTitle").val(),
                from:$(".semester_new #inputSemesterFrom").val(),
                to:$(".semester_new #inputSemesterTo").val(),
            },
            dataType:"json",
            success:function(data, textStatus, jqXHR)
            {
                if ( data.error )
                {
                    alert(data.error.join("\n"));
                }
                else {
                    // Not elegant. But robust.
                    location.href = "?q=admin";
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
//                alert("error: "+jQuery.parseJSON(jqXHR));
            }
        }); 
    }

    function resetSemester()
    {
        changeTextField($("#inputSemesterTitle"), "");
        changeTextField($("#inputSemesterFrom"), "");
        changeTextField($("#inputSemesterTo"), "");
        $("#submitSemester").unbind("click").click(function(){
            addSemester();
        });

        $(".changeSemester").unbind("click").click(function(){
            var ID = $(this).attr("id").substr(15);
            startSemesterEditing (ID);
            return false;
        });
        $(".deleteSemester").unbind("click").click(function(){
            var check = confirm(unescape("Dieses Semester mit allen Trainings und Anwesenheitsdaten löschen??"));
            if ( check == false )
            {
                return false;
            }
            ID = $(this).attr("id").substr(15);
            deleteSemester(ID);
            return false;
        });
    }

    resetSemester();

    function deleteTraining ( ID )
    {
        $.ajax({
            url:'ajax.php?action=deleteTraining',
            type:'POST',
            data:{ID:ID},
            dataType:"json",
            success:function(data, textStatus, jqXHR)
            {
                // Not elegant. But robust.
                location.href = "?q=admin";
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
//                alert("error: "+jQuery.parseJSON(jqXHR));
            }
        });
    }

    function editTraining(ID, semesterID){
        var row = $("#training_" + ID);
        $.ajax({
            url:'ajax.php?action=editTraining',
            type:'POST',
            data:{
                ID:ID,
                semester:semesterID,
                title:$("#inputTrainingTitle", row).val(),
                day:$("#inputTrainingDay", row).val(),
                from:$("#inputTrainingFrom", row).val(),
                to:$("#inputTrainingTo", row).val(),
            },
            dataType:"json",
            success:function(data, textStatus, jqXHR)
            {
                if ( data.error )
                {
                    alert(data.error);
                }
                else {
                    // Not elegant. But robust.
                    location.href = "?q=admin";
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
//                alert("error: "+jQuery.parseJSON(jqXHR));
            }
        });  
    }
    
    function startTrainingEditing(ID) {
         var row = $("#training_" + ID);
         row.removeClass("normalMode");
         row.addClass("waitMode");

         $.ajax({
            url:'ajax.php?action=loadTraining',
            type:'POST',
            data:{ID:ID},
            dataType:"json",
            success:function(data, textStatus, jqXHR)
            {
                row.removeClass("waitMode");
                row.addClass("editMode");

                changeTextField($("#inputTrainingTitle", row), data.title);
                $("#inputTrainingDay", row).val(data.day);
                changeTextField($("#inputTrainingFrom", row), data.fromF);
                changeTextField($("#inputTrainingTo", row), data.toF);
                $(".submit", row).unbind("click").click(function(){
                    var semester_trainings = $(this).closest(".semester_trainings");
                    var semesterID = semester_trainings.attr("id").substr(19);
                    editTraining(ID, semesterID);
                });

                $(".reset", row).unbind("click").click(function(){
                    endTrainingEditing(ID);
                });
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
            }
         });
    }

    function endTrainingEditing(ID) {
         var row = $("#training_" + ID);
         row.removeClass("waitMode");
         row.removeClass("editMode");
         row.addClass("normalMode");
    }

    function addTraining(semesterID, context)
    {
         $.ajax({
            url:'ajax.php?action=addTraining',
            type:'POST',
            data:{
                semesterID:semesterID,
                title:$("#inputTrainingTitle", context).val(),
                day:$("#inputTrainingDay", context).val(),
                from:$("#inputTrainingFrom", context).val(),
                to:$("#inputTrainingTo", context).val(),
            },
            dataType:"json",
            success:function(data, textStatus, jqXHR)
            {
                if ( data.error )
                {
                    alert(data.error.join("\n"));
                }
                else {
                    // Not elegant. But robust.
                    location.href = "?q=admin";
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
//                alert("error: "+jQuery.parseJSON(jqXHR));
            }
        }); 
    }

    function resetTraining()
    {
        changeTextField($("#inputTrainingTitle"), "");
        $("#inputTrainingDay").val("");
        changeTextField($("#inputTrainingFrom"), "");
        changeTextField($("#inputTrainingTo"), "");
        $(".submitTraining").unbind("click").click(function(){
            var semester_trainings = $(this).closest(".semester_trainings");
            var semesterID = semester_trainings.attr("id").substr(19);
            addTraining(semesterID, $(".training_new", semester_trainings));
        });

        $(".changeTraining").unbind("click").click(function(){
            ID = $(this).attr("id").substr(15);
            startTrainingEditing (ID);
            return false;
        });
        $(".deleteTraining").unbind("click").click(function(){
            var check = confirm(unescape("Dieses Training mit allen Anwesenheitsdaten löschen??"));
            if ( check == false )
            {
                return false;
            }
            ID = $(this).attr("id").substr(15);
            deleteTraining(ID);
            return false;
        });
        $(".exportTraining").unbind("click").click(function(){
            ID = $(this).attr("id").substr(15);
            location.href = "export.php?tid=" + ID;
            return false;
        });
    }

    resetTraining();

});
