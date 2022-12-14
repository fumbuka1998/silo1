<script type="text/javascript">


 var ge;

 $('a[href="#project_gantt_chart"]').on('shown.bs.tab', function() {
        var link_tab = $(this);
        if(link_tab.attr('initialized') != 'true'){

              var canWrite=true; //this is the default for test purposes

              // here starts gantt initialization
              ge = new GanttMaster();
              var workSpace = $("#ganttChart");
              var width = $(window).width()-120;
              var height = $(window).height()-230;
              workSpace.css({width:width,height:height});
              ge.init(workSpace);
              loadI18n(); //overwrite with localized ones

              //in order to force compute the best-fitting zoom level
              delete ge.gantt.zoom;

                loadGanttFromServer(link_tab.attr('project_id'));

              ge.editor.element.oneTime(100, "cl", function () {$(this).find("tr.emptyRow:first").click()});

              $(window).resize(function(){
               workSpace.css({width:width - 1,height:height - workSpace.position().top});
               workSpace.trigger("resize.gantt");
              }).oneTime(100,"resize",function(){$(this).trigger("resize")});

                $(this).attr('initialized','true');
        }
 });

/* $(function() {
  var canWrite=true; //this is the default for test purposes

  // here starts gantt initialization
  ge = new GanttMaster();
  var workSpace = $("#ganttChart");

  workSpace.css({width:$('#project_gantt_chart').width(),height:$('#project_gantt_chart').height()});

  ge.init(workSpace);
  loadI18n(); //overwrite with localized ones

  //in order to force compute the best-fitting zoom level
  delete ge.gantt.zoom;


  var project=loadFromLocalStorage();


  if (!project.canWrite)
   $(".ganttButtonBar button.requireWrite").attr("disabled","true");

  ge.loadProject(project);
  ge.checkpoint(); //empty the undo stack

  ge.editor.element.oneTime(100, "cl", function () {$(this).find("tr.emptyRow:first").click()});

  $(window).resize(function(){
   workSpace.css({width:$(window).width() - 1,height:$(window).height() - workSpace.position().top});
   workSpace.trigger("resize.gantt");
  }).oneTime(150,"resize",function(){$(this).trigger("resize")});
 });*/


 function loadGanttFromServer(project_id, callback) {
      start_spinner();
     $.getJSON(base_url + "projects/load_project_gantt_chart/"+project_id, {CM:"LOADPROJECT"}, function(response) {
         //console.debug(response);
         //actualize data
         var ret = response.project;
         if (!response.project.canWrite) {
             $(".ganttButtonBar button.requireWrite").attr("disabled", "true");
         }

         ge.loadProject(ret);
         ge.checkpoint(); //empty the undo stack
         stop_spinner();
     });
 }


 function saveGanttOnServer() {

  //this is a simulation: save data to the local storage or to the textarea
  saveInLocalStorage();

  /*
   var prj = ge.saveProject();

   delete prj.resources;
   delete prj.roles;

   var prof = new Profiler("saveServerSide");
   prof.reset();

   if (ge.deletedTaskIds.length>0) {
   if (!confirm("TASK_THAT_WILL_BE_REMOVED\n"+ge.deletedTaskIds.length)) {
   return;
   }
   }

   $.ajax("ganttAjaxController.jsp", {
   dataType:"json",
   data: {CM:"SVPROJECT",prj:JSON.stringify(prj)},
   type:"POST",

   success: function(response) {
   if (response.ok) {
   prof.stop();
   if (response.project) {
   ge.loadProject(response.project); //must reload as "tmp_" ids are now the good ones
   } else {
   ge.reset();
   }
   } else {
   var errMsg="Errors saving project\n";
   if (response.message) {
   errMsg=errMsg+response.message+"\n";
   }

   if (response.errorMessages.length) {
   errMsg += response.errorMessages.join("\n");
   }

   alert(errMsg);
   }
   }

   });
   */
 }

 function newProject(){
  clearGantt();
 }


 //-------------------------------------------  Create some demo data ------------------------------------------------------
 function setRoles() {
  ge.roles = [
   {
    id:"tmp_1",
    name:"Project Manager"
   },
   {
    id:"tmp_2",
    name:"Worker"
   },
   {
    id:"tmp_3",
    name:"Stakeholder"
   },
   {
    id:"tmp_4",
    name:"Customer"
   }
  ];
 }

 function setResource() {
  var res = [];
  for (var i = 1; i <= 10; i++) {
   res.push({id:"tmp_" + i,name:"Resource " + i});
  }
  ge.resources = res;
 }


 function editResources(){

 }

 function clearGantt() {
  ge.reset();
 }

 function loadI18n() {
  GanttMaster.messages = {
   "CANNOT_WRITE":                  "CANNOT_WRITE",
   "CHANGE_OUT_OF_SCOPE":"NO_RIGHTS_FOR_UPDATE_PARENTS_OUT_OF_EDITOR_SCOPE",
   "START_IS_MILESTONE":"START_IS_MILESTONE",
   "END_IS_MILESTONE":"END_IS_MILESTONE",
   "TASK_HAS_CONSTRAINTS":"TASK_HAS_CONSTRAINTS",
   "GANTT_ERROR_DEPENDS_ON_OPEN_TASK":"GANTT_ERROR_DEPENDS_ON_OPEN_TASK",
   "GANTT_ERROR_DESCENDANT_OF_CLOSED_TASK":"GANTT_ERROR_DESCENDANT_OF_CLOSED_TASK",
   "TASK_HAS_EXTERNAL_DEPS":"TASK_HAS_EXTERNAL_DEPS",
   "GANTT_ERROR_LOADING_DATA_TASK_REMOVED":"GANTT_ERROR_LOADING_DATA_TASK_REMOVED",
   "ERROR_SETTING_DATES":"ERROR_SETTING_DATES",
   "CIRCULAR_REFERENCE":"CIRCULAR_REFERENCE",
   "CANNOT_DEPENDS_ON_ANCESTORS":"CANNOT_DEPENDS_ON_ANCESTORS",
   "CANNOT_DEPENDS_ON_DESCENDANTS":"CANNOT_DEPENDS_ON_DESCENDANTS",
   "INVALID_DATE_FORMAT":"INVALID_DATE_FORMAT",
   "TASK_MOVE_INCONSISTENT_LEVEL":"TASK_MOVE_INCONSISTENT_LEVEL",

   "GANTT_QUARTER_SHORT":"trim.",
   "GANTT_SEMESTER_SHORT":"sem."
  };
 }



 //-------------------------------------------  Get project file as JSON (used for migrate project from gantt to Teamwork) ------------------------------------------------------
 function getFile() {
  $("#gimBaPrj").val(JSON.stringify(ge.saveProject()));
  $("#gimmeBack").submit();
  $("#gimBaPrj").val("");

  /*  var uriContent = "data:text/html;charset=utf-8," + encodeURIComponent(JSON.stringify(prj));
   neww=window.open(uriContent,"dl");*/
 }


 function loadFromLocalStorage() {
  var ret;
  if (localStorage) {
   if (localStorage.getObject("projectGantt")) {
    ret = localStorage.getObject("projectGantt");
   }
  }

  //if not found create a new example task
  /*if (!ret || !ret.tasks || ret.tasks.length == 0){
   ret= {"tasks":    [
    {"id": -1, "name": "Gantt editor", "progress": 0, "progressByWorklog": false, "relevance": 0, "type": "", "typeId": "", "description": "", "code": "", "level": 0, "status": "STATUS_ACTIVE", "depends": "", "canWrite": true, "start": 1396994400000, "duration": 20, "end": 1399586399999, "startIsMilestone": false, "endIsMilestone": false, "collapsed": false, "assigs": [], "hasChild": true},
    {"id": -2, "name": "coding", "progress": 0, "progressByWorklog": false, "relevance": 0, "type": "", "typeId": "", "description": "", "code": "", "level": 1, "status": "STATUS_ACTIVE", "depends": "", "canWrite": true, "start": 1396994400000, "duration": 10, "end": 1398203999999, "startIsMilestone": false, "endIsMilestone": false, "collapsed": false, "assigs": [], "hasChild": true},
    {"id": -3, "name": "gantt part", "progress": 0, "progressByWorklog": false, "relevance": 0, "type": "", "typeId": "", "description": "", "code": "", "level": 2, "status": "STATUS_ACTIVE", "depends": "", "canWrite": true, "start": 1396994400000, "duration": 2, "end": 1397167199999, "startIsMilestone": false, "endIsMilestone": false, "collapsed": false, "assigs": [], "hasChild": false},
    {"id": -4, "name": "editor part", "progress": 0, "progressByWorklog": false, "relevance": 0, "type": "", "typeId": "", "description": "", "code": "", "level": 2, "status": "STATUS_SUSPENDED", "depends": "3", "canWrite": true, "start": 1397167200000, "duration": 4, "end": 1397685599999, "startIsMilestone": false, "endIsMilestone": false, "collapsed": false, "assigs": [], "hasChild": false},
    {"id": -5, "name": "testing", "progress": 0, "progressByWorklog": false, "relevance": 0, "type": "", "typeId": "", "description": "", "code": "", "level": 1, "status": "STATUS_SUSPENDED", "depends": "2:5", "canWrite": true, "start": 1398981600000, "duration": 5, "end": 1399586399999, "startIsMilestone": false, "endIsMilestone": false, "collapsed": false, "assigs": [], "hasChild": true},
    {"id": -6, "name": "test on safari", "progress": 0, "progressByWorklog": false, "relevance": 0, "type": "", "typeId": "", "description": "", "code": "", "level": 2, "status": "STATUS_SUSPENDED", "depends": "", "canWrite": true, "start": 1398981600000, "duration": 2, "end": 1399327199999, "startIsMilestone": false, "endIsMilestone": false, "collapsed": false, "assigs": [], "hasChild": false},
    {"id": -7, "name": "test on ie", "progress": 0, "progressByWorklog": false, "relevance": 0, "type": "", "typeId": "", "description": "", "code": "", "level": 2, "status": "STATUS_SUSPENDED", "depends": "6", "canWrite": true, "start": 1399327200000, "duration": 3, "end": 1399586399999, "startIsMilestone": false, "endIsMilestone": false, "collapsed": false, "assigs": [], "hasChild": false},
    {"id": -8, "name": "test on chrome", "progress": 0, "progressByWorklog": false, "relevance": 0, "type": "", "typeId": "", "description": "", "code": "", "level": 2, "status": "STATUS_SUSPENDED", "depends": "6", "canWrite": true, "start": 1399327200000, "duration": 2, "end": 1399499999999, "startIsMilestone": false, "endIsMilestone": false, "collapsed": false, "assigs": [], "hasChild": false}
   ], "selectedRow": 2, "deletedTaskIds": [],
    "resources": [
     {"id": "tmp_1", "name": "Resource 1"},
     {"id": "tmp_2", "name": "Resource 2"},
     {"id": "tmp_3", "name": "Resource 3"},
     {"id": "tmp_4", "name": "Resource 4"}
    ],
    "roles":       [
     {"id": "tmp_1", "name": "Project Manager"},
     {"id": "tmp_2", "name": "Worker"},
     {"id": "tmp_3", "name": "Stakeholder"},
     {"id": "tmp_4", "name": "Customer"}
    ], "canWrite":    true, "canWriteOnParent": true, "zoom": "w3"}


   //actualize data
   var offset=new Date().getTime()-ret.tasks[0].start;
   for (var i=0;i<ret.tasks.length;i++) {
    ret.tasks[i].start = ret.tasks[i].start + offset;
   }
  }*/
  return ret;
 }


 function saveInLocalStorage() {
  var prj = ge.saveProject();
  if (localStorage) {
    localStorage.setObject("teamworkGantDemo", prj);
  }
 }


 //-------------------------------------------  Open a black popup for managing resources. This is only an axample of implementation (usually resources come from server) ------------------------------------------------------
 function editResources(){

  //make resource editor
  var resourceEditor = $.JST.createFromTemplate({}, "RESOURCE_EDITOR");
  var resTbl=resourceEditor.find("#resourcesTable");

  for (var i=0;i<ge.resources.length;i++){
   var res=ge.resources[i];
   resTbl.append($.JST.createFromTemplate(res, "RESOURCE_ROW"))
  }

  //bind add resource
  resourceEditor.find("#addResource").click(function(){
   resTbl.append($.JST.createFromTemplate({id:"new",name:"resource"}, "RESOURCE_ROW"))
  });

  //bind save event
  resourceEditor.find("#resSaveButton").click(function(){
   var newRes=[];
   //find for deleted res
   for (var i=0;i<ge.resources.length;i++){
    var res=ge.resources[i];
    var row = resourceEditor.find("[resId="+res.id+"]");
    if (row.length>0){
     //if still there save it
     var name = row.find("input[name]").val();
     if (name && name!="")
      res.name=name;
     newRes.push(res);
    } else {
     //remove assignments
     for (var j=0;j<ge.tasks.length;j++){
      var task=ge.tasks[j];
      var newAss=[];
      for (var k=0;k<task.assigs.length;k++){
       var ass=task.assigs[k];
       if (ass.resourceId!=res.id)
        newAss.push(ass);
      }
      task.assigs=newAss;
     }
    }
   }

   //loop on new rows
   var cnt=0;
   resourceEditor.find("[resId=new]").each(function(){
    cnt++;
    var row = $(this);
    var name = row.find("input[name]").val();
    if (name && name!="")
     newRes.push (new Resource("tmp_"+new Date().getTime()+"_"+cnt,name));
   });

   ge.resources=newRes;

   closeBlackPopup();

  });


  var ndo = createModalPopup(400, 500).append(resourceEditor);
 }
</script>

<div id="gantEditorTemplates" style="display:none;">
 <div class="__template__" type="GANTBUTTONS"><!--
  <div class="ganttButtonBar noprint">
    <div class="buttons">

    <span class="ganttButtonSeparator"></span>
      <button onclick="$('#ganttChart').trigger('zoomMinus.gantt'); return false;" class="button textual icon " title="zoom out"><span class="teamworkIcon">)</span></button>
      <button onclick="$('#ganttChart').trigger('zoomPlus.gantt');return false;" class="button textual icon " title="zoom in"><span class="teamworkIcon">(</span></button>

    <span class="ganttButtonSeparator requireCanSeeCriticalPath"></span>
      <button onclick="ge.splitter.resize(.1);return false;" class="button textual icon" ><span class="teamworkIcon">F</span></button>
      <button onclick="ge.splitter.resize(50);return false;" class="button textual icon" ><span class="teamworkIcon">O</span></button>
      <button onclick="ge.splitter.resize(100);return false;" class="button textual icon"><span class="teamworkIcon">R</span></button>

   </div></div>
  --></div>

 <div class="__template__" type="TASKSEDITHEAD"><!--
  <table class="gdfTable" cellspacing="0" cellpadding="0">
    <thead>
    <tr style="height:40px">
      <th class="gdfColHeader" style="width:35px; border-right: none"></th>
      <th class="gdfColHeader" style="width:25px;"></th>
      <th class="gdfColHeader gdfResizable" style="width:300px;">Name</th>
      <th class="gdfColHeader gdfResizable" style="width:80px;">Start</th>
     <th class="gdfColHeader gdfResizable" style="width:80px;">End</th>
      <th class="gdfColHeader gdfResizable" style="width:50px;">Dur.</th>
      <th class="gdfColHeader gdfResizable" style="width:50px;">%</th>
    </tr>
    </thead>
  </table>
  --></div>

 <div class="__template__" type="TASKROW"><!--
  <tr taskId="(#=obj.id#)" class="taskEditRow (#=obj.isParent()?'isParent':''#) (#=obj.collapsed?'collapsed':''#)" level="(#=level#)">
    <th class="gdfCell (#=obj.isParent()?'':'edit_bk'#) " align="right" style="cursor:pointer;">
    <span class="taskRowIndex">(#=obj.getRow()+1#)</span> (#=obj.isParent()?'':'<span class="teamworkIcon" style="font-size:12px;" >e</span>'#)
    </th>
    <td class="gdfCell noClip" align="center"><div class="taskStatus cvcColorSquare" status="(#=obj.status#)"></div></td>
    <td class="gdfCell indentCell" style="padding-left:(#=obj.level*10+18#)px;">
      <div class="exp-controller" align="center"></div>
      <input type="text" name="name" value="(#=obj.name#)" placeholder="name">
    </td>
    <td class="gdfCell"><input type="text" name="start"  value="" class="date"></td>
    <td class="gdfCell"><input type="text" name="end" value="" class="date"></td>
    <td class="gdfCell"><input type="text" name="duration" autocomplete="off" value="(#=obj.duration#)"></td>
    <td class="gdfCell"><input type="text" name="progress" class="validated" entrytype="PERCENTILE" autocomplete="off" value="(#=obj.progress?obj.progress:''#)" (#=obj.progressByWorklog?"readOnly":""#)></td>
  --></div>

 <div class="__template__" type="TASKEMPTYROW"><!--
  <tr class="taskEditRow emptyRow" >
    <th class="gdfCell" align="right"></th>
    <td class="gdfCell noClip" align="center"></td>
    <td class="gdfCell"></td>
    <td class="gdfCell"></td>
    <td class="gdfCell"></td>
    <td class="gdfCell"></td>
    <td class="gdfCell"></td>
    <td class="gdfCell"></td>
    <td class="gdfCell"></td>
    <td class="gdfCell"></td>
  </tr>
  --></div>

 <div class="__template__" type="TASKBAR"><!--
  <div class="taskBox taskBoxDiv" taskId="(#=obj.id#)" >
    <div class="layout (#=obj.hasExternalDep?'extDep':''#)">
      <div class="taskStatus" status="(#=obj.status#)"></div>
      <div class="taskProgress" style="width:(#=obj.progress>100?100:obj.progress#)%; background-color:(#=obj.progress>100?'red':'rgb(153,255,51);'#);"></div>
      <div class="milestone (#=obj.startIsMilestone?'active':''#)" ></div>

      <div class="taskLabel"></div>
      <div class="milestone end (#=obj.endIsMilestone?'active':''#)" ></div>
    </div>
  </div>
  --></div>


 <div class="__template__" type="CHANGE_STATUS"><!--
    <div class="taskStatusBox">
      <div class="taskStatus cvcColorSquare" status="STATUS_ACTIVE" title="active"></div>
      <div class="taskStatus cvcColorSquare" status="STATUS_DONE" title="completed"></div>
      <div class="taskStatus cvcColorSquare" status="STATUS_FAILED" title="failed"></div>
      <div class="taskStatus cvcColorSquare" status="STATUS_SUSPENDED" title="suspended"></div>
      <div class="taskStatus cvcColorSquare" status="STATUS_UNDEFINED" title="undefined"></div>
    </div>
  --></div>





 <div class="__template__" type="TASK_EDITOR"><!--
  <div class="ganttTaskEditor">
    <h2 class="taskData">Task editor</h2>
    <table  cellspacing="1" cellpadding="5" width="100%" class="taskData table" border="0">
          <tr>
        <td width="200" style="height: 80px"  valign="top">
          <label for="code">code/short name</label><br>
          <input type="text" name="code" id="code" value="" size=15 class="formElements" autocomplete='off' maxlength=255 style='width:100%' oldvalue="1">
        </td>
        <td colspan="3" valign="top"><label for="name" class="required">name</label><br><input type="text" name="name" id="name"class="formElements" autocomplete='off' maxlength=255 style='width:100%' value="" required="true" oldvalue="1"></td>
          </tr>


      <tr class="dateRow">
        <td nowrap="">
          <div style="position:relative">
            <label for="start">start</label>&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="checkbox" id="startIsMilestone" name="startIsMilestone" value="yes"> &nbsp;<label for="startIsMilestone">is milestone</label>&nbsp;
            <br><input type="text" name="start" id="start" size="8" class="formElements dateField validated date" autocomplete="off" maxlength="255" value="" oldvalue="1" entrytype="DATE">
            <span title="calendar" id="starts_inputDate" class="teamworkIcon openCalendar" onclick="$(this).dateField({inputField:$(this).prevAll(':input:first'),isSearchField:false});">m</span>          </div>
        </td>
        <td nowrap="">
          <label for="end">End</label>&nbsp;&nbsp;&nbsp;&nbsp;
          <input type="checkbox" id="endIsMilestone" name="endIsMilestone" value="yes"> &nbsp;<label for="endIsMilestone">is milestone</label>&nbsp;
          <br><input type="text" name="end" id="end" size="8" class="formElements dateField validated date" autocomplete="off" maxlength="255" value="" oldvalue="1" entrytype="DATE">
          <span title="calendar" id="ends_inputDate" class="teamworkIcon openCalendar" onclick="$(this).dateField({inputField:$(this).prevAll(':input:first'),isSearchField:false});">m</span>
        </td>
        <td nowrap="" >
          <label for="duration" class=" ">Days</label><br>
          <input type="text" name="duration" id="duration" size="4" class="formElements validated durationdays" title="Duration is in working days." autocomplete="off" maxlength="255" value="" oldvalue="1" entrytype="DURATIONDAYS">&nbsp;
        </td>
      </tr>

      <tr>
        <td  colspan="2">
          <label for="status" class=" ">status</label><br>
          <select id="status" name="status" class="taskStatus" status="(#=obj.status#)"  onchange="$(this).attr('STATUS',$(this).val());">
            <option value="STATUS_ACTIVE" class="taskStatus" status="STATUS_ACTIVE" >active</option>
            <option value="STATUS_SUSPENDED" class="taskStatus" status="STATUS_SUSPENDED" >suspended</option>
            <option value="STATUS_DONE" class="taskStatus" status="STATUS_DONE" >completed</option>
            <option value="STATUS_FAILED" class="taskStatus" status="STATUS_FAILED" >failed</option>
            <option value="STATUS_UNDEFINED" class="taskStatus" status="STATUS_UNDEFINED" >undefined</option>
          </select>
        </td>

        <td valign="top" nowrap>
          <label>progress</label><br>
          <input type="text" name="progress" id="progress" size="7" class="formElements validated percentile" autocomplete="off" maxlength="255" value="" oldvalue="1" entrytype="PERCENTILE">
        </td>
      </tr>

          </tr>
          <tr>
            <td colspan="4">
              <label for="description">Description</label><br>
              <textarea rows="3" cols="30" id="description" name="description" class="formElements" style="width:100%"></textarea>
            </td>
          </tr>
        </table>

    <h2>Assignments</h2>
  <table  cellspacing="1" cellpadding="0" width="100%" id="assigsTable">
    <tr>
      <th style="width:100px;">name</th>
      <th style="width:70px;">Role</th>
      <th style="width:30px;">est.wklg.</th>
      <th style="width:30px;" id="addAssig"><span class="teamworkIcon" style="cursor: pointer">+</span></th>
    </tr>
  </table>

  <div style="text-align: right; padding-top: 20px">
    <span id="saveButton" class="button first" onClick="$(this).trigger('saveFullEditor.gantt');">Save</span>
  </div>

  </div>
  --></div>



 <div class="__template__" type="ASSIGNMENT_ROW"><!--
  <tr taskId="(#=obj.task.id#)" assId="(#=obj.assig.id#)" class="assigEditRow" >
    <td ><select name="resourceId"  class="formElements" (#=obj.assig.id.indexOf("tmp_")==0?"":"disabled"#) ></select></td>
    <td ><select type="select" name="roleId"  class="formElements"></select></td>
    <td ><input type="text" name="effort" value="(#=getMillisInHoursMinutes(obj.assig.effort)#)" size="5" class="formElements"></td>
    <td align="center"><span class="teamworkIcon delAssig del" style="cursor: pointer">d</span></td>
  </tr>
  --></div>



 <div class="__template__" type="RESOURCE_EDITOR"><!--
  <div class="resourceEditor" style="padding: 5px;">

    <h2>Project team</h2>
    <table  cellspacing="1" cellpadding="0" width="100%" id="resourcesTable">
      <tr>
        <th style="width:100px;">name</th>
        <th style="width:30px;" id="addResource"><span class="teamworkIcon" style="cursor: pointer">+</span></th>
      </tr>
    </table>

    <div style="text-align: right; padding-top: 20px"><button id="resSaveButton" class="button big">Save</button></div>
  </div>
  --></div>



 <div class="__template__" type="RESOURCE_ROW"><!--
  <tr resId="(#=obj.id#)" class="resRow" >
    <td ><input type="text" name="name" value="(#=obj.name#)" style="width:100%;" class="formElements"></td>
    <td align="center"><span class="teamworkIcon delRes del" style="cursor: pointer">d</span></td>
  </tr>
  --></div>


</div>


<script type="text/javascript">
 $.JST.loadDecorator("RESOURCE_ROW", function(resTr, res){
  resTr.find(".delRes").click(function(){$(this).closest("tr").remove()});
 });

 $.JST.loadDecorator("ASSIGNMENT_ROW", function(assigTr, taskAssig){
  var resEl = assigTr.find("[name=resourceId]");
  var opt = $("<option>");
  resEl.append(opt);
  for(var i=0; i< taskAssig.task.master.resources.length;i++){
   var res = taskAssig.task.master.resources[i];
   opt = $("<option>");
   opt.val(res.id).html(res.name);
   if(taskAssig.assig.resourceId == res.id)
    opt.attr("selected", "true");
   resEl.append(opt);
  }
  var roleEl = assigTr.find("[name=roleId]");
  for(var i=0; i< taskAssig.task.master.roles.length;i++){
   var role = taskAssig.task.master.roles[i];
   var optr = $("<option>");
   optr.val(role.id).html(role.name);
   if(taskAssig.assig.roleId == role.id)
    optr.attr("selected", "true");
   roleEl.append(optr);
  }

  if(taskAssig.task.master.permissions.canWrite && taskAssig.task.canWrite){
   assigTr.find(".delAssig").click(function(){
    var tr = $(this).closest("[assId]").fadeOut(200, function(){$(this).remove()});
   });
  }

 });


 function loadI18n(){
  GanttMaster.messages = {
   "CANNOT_WRITE":"No permission to change the following task:",
   "CHANGE_OUT_OF_SCOPE":"Project update not possible as you lack rights for updating a parent project.",
   "START_IS_MILESTONE":"Start date is a milestone.",
   "END_IS_MILESTONE":"End date is a milestone.",
   "TASK_HAS_CONSTRAINTS":"Task has constraints.",
   "GANTT_ERROR_DEPENDS_ON_OPEN_TASK":"Error: there is a dependency on an open task.",
   "GANTT_ERROR_DESCENDANT_OF_CLOSED_TASK":"Error: due to a descendant of a closed task.",
   "TASK_HAS_EXTERNAL_DEPS":"This task has external dependencies.",
   "GANNT_ERROR_LOADING_DATA_TASK_REMOVED":"GANNT_ERROR_LOADING_DATA_TASK_REMOVED",
   "CIRCULAR_REFERENCE":"Circular reference.",
   "CANNOT_DEPENDS_ON_ANCESTORS":"Cannot depend on ancestors.",
   "INVALID_DATE_FORMAT":"The data inserted are invalid for the field format.",
   "GANTT_ERROR_LOADING_DATA_TASK_REMOVED":"An error has occurred while loading the data. A task has been trashed.",
   "CANNOT_CLOSE_TASK_IF_OPEN_ISSUE":"Cannot close a task with open issues",
   "TASK_MOVE_INCONSISTENT_LEVEL":"You cannot exchange tasks of different depth.",
   "GANTT_QUARTER_SHORT":"Quarter",
   "GANTT_SEMESTER_SHORT":"Sem",
   "CANNOT_MOVE_TASK":"CANNOT_MOVE_TASK",
   "PLEASE_SAVE_PROJECT":"PLEASE_SAVE_PROJECT"
  };
 }



 function createNewResource(el) {
  var row = el.closest("tr[taskid]");
  var name = row.find("[name=resourceId_txt]").val();
  var url = contextPath + "/applications/teamwork/resource/resourceNew.jsp?CM=ADD&name=" + encodeURI(name);

  openBlackPopup(url, 700, 320, function (response) {
   //fillare lo smart combo
   if (response && response.resId && response.resName) {
    //fillare lo smart combo e chiudere l'editor
    row.find("[name=resourceId]").val(response.resId);
    row.find("[name=resourceId_txt]").val(response.resName).focus().blur();
   }

  });
 }
</script>
