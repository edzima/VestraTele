(function(e){function t(t){for(var a,i,c=t[0],s=t[1],l=t[2],d=0,b=[];d<c.length;d++)i=c[d],Object.prototype.hasOwnProperty.call(o,i)&&o[i]&&b.push(o[i][0]),o[i]=0;for(a in s)Object.prototype.hasOwnProperty.call(s,a)&&(e[a]=s[a]);u&&u(t);while(b.length)b.shift()();return r.push.apply(r,l||[]),n()}function n(){for(var e,t=0;t<r.length;t++){for(var n=r[t],a=!0,c=1;c<n.length;c++){var s=n[c];0!==o[s]&&(a=!1)}a&&(r.splice(t--,1),e=i(i.s=n[0]))}return e}var a={},o={app:0},r=[];function i(t){if(a[t])return a[t].exports;var n=a[t]={i:t,l:!1,exports:{}};return e[t].call(n.exports,n,n.exports,i),n.l=!0,n.exports}i.m=e,i.c=a,i.d=function(e,t,n){i.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:n})},i.r=function(e){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},i.t=function(e,t){if(1&t&&(e=i(e)),8&t)return e;if(4&t&&"object"===typeof e&&e&&e.__esModule)return e;var n=Object.create(null);if(i.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var a in e)i.d(n,a,function(t){return e[t]}.bind(null,a));return n},i.n=function(e){var t=e&&e.__esModule?function(){return e["default"]}:function(){return e};return i.d(t,"a",t),t},i.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},i.p="/";var c=window["webpackJsonp"]=window["webpackJsonp"]||[],s=c.push.bind(c);c.push=t,c=c.slice();for(var l=0;l<c.length;l++)t(c[l]);var u=s;r.push([0,"chunk-vendors"]),n()})({0:function(e,t,n){e.exports=n("cd49")},"015d":function(e,t,n){},"1c3c":function(e,t,n){},"295a":function(e,t,n){"use strict";n("40ba")},3813:function(e,t,n){"use strict";n("61c1")},"40ba":function(e,t,n){},"61c1":function(e,t,n){},"63fb":function(e,t,n){"use strict";n("e80f")},"73db":function(e,t,n){"use strict";n("015d")},"94cb":function(e,t,n){"use strict";n("e85a")},a2fc:function(e,t,n){},aa2a:function(e,t,n){"use strict";n("a2fc")},c195:function(e,t,n){},cd49:function(e,t,n){"use strict";n.r(t);n("e260"),n("e6cf"),n("cca6"),n("a79d");var a=n("2b0e"),o=function(){var e=this,t=e._self._c;e._self._setupProxy;return t("div",{staticClass:"filter-calendar"},[e.notesEnabled?t("BootstrapPopup",{ref:"notesPopup",attrs:{title:this.notePopupTitle,outerDissmisable:""}},[t("CalendarNotes",{attrs:{notes:e.dayNotes,onNoteAdd:e.addNote,onNoteDelete:e.deleteNote,onNoteUpdate:e.editNoteText,editable:""}})],1):e._e(),t("FilterCalendar",{ref:"calendarFilter",attrs:{filterGroups:e.filterGroups,notesEnabled:e.notesEnabled,eventSources:e.eventSources,editable:e.allowUpdate},on:{dateClick:e.dateClick,dateDoubleClick:e.dateDoubleClick,eventEdit:e.updateDates,eventClick:e.eventClick}})],1)},r=[],i=n("c7eb"),c=n("1da1"),s=n("2909"),l=n("d4ec"),u=n("bee2"),d=n("257e"),b=n("262e"),p=n("2caf"),v=n("ade3"),f=(n("99af"),n("d81d"),n("4de4"),n("d3b7"),n("3ca3"),n("ddb0"),n("9861"),n("159b"),n("b0c0"),n("bf19"),n("9ab4")),h=n("60a3"),O=function(){var e=this,t=e._self._c;e._self._setupProxy;return t("div",{staticClass:"filter-group"},[e.filterGroup.title?t("h3",[e._v(e._s(e.filterGroup.title))]):e._e(),t("ul",{staticClass:"filters-nav nav nav-pills"},e._l(e.filterGroup.filters,(function(n){return t("li",{staticClass:"filter-item nav-item",class:{active:n.isActive},attrs:{"data-id":n.value}},[t("a",{staticClass:"btn nav-link filter-btn",style:e.itemStyle(n),on:{click:function(t){return e.filterClick(n)}}},[e._v(" "+e._s(n.label)+" ")])])})),0)])},j=[],y=function(e){Object(b["a"])(n,e);var t=Object(p["a"])(n);function n(){var e;Object(l["a"])(this,n);for(var a=arguments.length,o=new Array(a),r=0;r<a;r++)o[r]=arguments[r];return e=t.call.apply(t,[this].concat(o)),Object(v["a"])(Object(d["a"])(e),"filterGroup",void 0),e}return Object(u["a"])(n,[{key:"itemStyle",value:function(e){return{backgroundColor:e.color}}},{key:"filterClick",value:function(e){var t=this.newGroupState(e);this.$emit("groupUpdate",t)}},{key:"newGroupState",value:function(e){var t=this.filterGroup;return t.filters=this.filterGroup.filters.map((function(t){var n=t;return t.value===e.value&&(n.isActive=!e.isActive),n})),t}}]),n}(h["d"]);Object(f["a"])([Object(h["b"])()],y.prototype,"filterGroup",void 0),y=Object(f["a"])([Object(h["a"])({})],y);var k=y,m=k,C=(n("3813"),n("2877")),g=Object(C["a"])(m,O,j,!1,null,null,null),N=g.exports,E=function(){var e=this,t=e._self._c;e._self._setupProxy;return t("div",{staticClass:"calendar-notes"},[e.isOperation("view")?["view"===e.operation?t("NotesList",{attrs:{notes:e.localNotes,confirmDelete:"",editable:""},on:{deleteClick:e.deleteNote,editClick:e.editNote}}):e._e(),e.localNotes.length?e._e():t("p",{staticClass:"note-placeholder text-primary"},[e._v(" Brak notatek ")]),t("Button",{staticClass:"btn btn-primary",on:{click:e.addNote}},[e._v("Dodaj Notatkę")])]:e._e(),e.isOperation("edit")?[t("NoteEditor",{attrs:{note:e.noteToEdit,"auto-focus":""},on:{discardChanges:e.discardEditChanges,saveEditedNote:e.noteChangesHandler}})]:e._e()],2)},x=[],w=n("5530"),_=(n("14d9"),function(){var e=this,t=e._self._c;e._self._setupProxy;return e.visible?t("div",{staticClass:"modal-wrapper",on:{click:e.outerClick}},[t("div",{staticClass:"modal show",attrs:{"aria-labelledby":"myModalLabel",id:"myModal",role:"dialog",tabindex:"-1"},on:{click:function(e){e.stopPropagation()}}},[t("div",{staticClass:"modal-dialog",attrs:{role:"document"}},[t("div",{staticClass:"modal-content"},[t("div",{staticClass:"modal-header"},[t("button",{staticClass:"close",attrs:{"aria-label":"Close",type:"button"},on:{click:function(t){return t.preventDefault(),e.hide.apply(null,arguments)}}},[t("span",{attrs:{"aria-hidden":"true"}},[e._v("×")])]),e.title?t("h4",{staticClass:"modal-title"},[e._v(e._s(e.title))]):e._e()]),t("div",{staticClass:"modal-body"},[e._t("default")],2)])])])]):e._e()}),P=[],D=function(e){Object(b["a"])(n,e);var t=Object(p["a"])(n);function n(){var e;Object(l["a"])(this,n);for(var a=arguments.length,o=new Array(a),r=0;r<a;r++)o[r]=arguments[r];return e=t.call.apply(t,[this].concat(o)),Object(v["a"])(Object(d["a"])(e),"title",void 0),Object(v["a"])(Object(d["a"])(e),"outerDissmisable",void 0),Object(v["a"])(Object(d["a"])(e),"visible",!1),e}return Object(u["a"])(n,[{key:"show",value:function(){this.visible=!0}},{key:"hide",value:function(){this.visible=!1}},{key:"outerClick",value:function(){this.outerDissmisable&&this.hide()}}]),n}(h["d"]);Object(f["a"])([Object(h["b"])()],D.prototype,"title",void 0),Object(f["a"])([Object(h["b"])({type:Boolean,default:function(){return!1}})],D.prototype,"outerDissmisable",void 0),D=Object(f["a"])([Object(h["a"])({})],D);var S=D,T=S,U=(n("73db"),Object(C["a"])(T,_,P,!1,null,"300ba807",null)),I=U.exports,R=(n("498a"),function(){var e=this,t=e._self._c;e._self._setupProxy;return t("div",{staticClass:"note-edit-container"},[t("div",{staticClass:"form-group"},[t("label",{attrs:{for:"note-editor"}},[e._v("Edytuj notatkę")]),t("textarea",{directives:[{name:"model",rawName:"v-model.trim",value:e.newNote.content,expression:"newNote.content",modifiers:{trim:!0}}],ref:"noteEditor",staticClass:"form-control",attrs:{id:"note-editor",rows:"3"},domProps:{value:e.newNote.content},on:{input:function(t){t.target.composing||e.$set(e.newNote,"content",t.target.value.trim())},blur:function(t){return e.$forceUpdate()}}}),t("div",{staticClass:"note-editor-controls"},[t("button",{staticClass:"btn btn-primary note-edit-controll",class:{disabled:!e.isNoteEdited},attrs:{disabled:!e.isNoteEdited,type:"button"},on:{click:e.saveEditNote}},[e._v("Zapisz")]),t("button",{staticClass:"btn btn-danger note-edit-controll",attrs:{type:"button"},on:{click:e.discardChanges}},[e._v("anuluj")])])])])}),A=[],F={title:"Usunąć notatkę?",text:"Ta operacja nie może zostać cofnięta",icon:"warning",showCancelButton:!0,confirmButtonText:"Tak usuń!",confirmButtonColor:"#95a2a9",cancelButtonText:"Nie, zachowaj!",cancelButtonColor:"#0c9cff",showCloseButton:!0},B={title:"Zapisać notatkę?",text:"Ta operacja nie może zostać cofnięta",icon:"warning",showCancelButton:!0,confirmButtonText:"Tak zapisz!",confirmButtonColor:"#0c9cff",cancelButtonText:"Nie, cofnij!",cancelButtonColor:"#95a2a9",showCloseButton:!0},G=function(e){Object(b["a"])(n,e);var t=Object(p["a"])(n);function n(){var e;Object(l["a"])(this,n);for(var a=arguments.length,o=new Array(a),r=0;r<a;r++)o[r]=arguments[r];return e=t.call.apply(t,[this].concat(o)),Object(v["a"])(Object(d["a"])(e),"note",void 0),Object(v["a"])(Object(d["a"])(e),"autoFocus",void 0),Object(v["a"])(Object(d["a"])(e),"confirmEdit",void 0),Object(v["a"])(Object(d["a"])(e),"newNote",{id:0,content:""}),Object(v["a"])(Object(d["a"])(e),"isNoteEdited",!1),e}return Object(u["a"])(n,[{key:"noteTextArea",get:function(){return this.$refs.noteEditor}},{key:"onPropertyChanged",value:function(){this.isNoteEdited=this.newNote.content!==this.note.content}},{key:"mounted",value:function(){this.autoFocus&&this.noteTextArea.focus(),this.newNote=Object(w["a"])({},this.note)}},{key:"saveEditNote",value:function(){var e=Object(c["a"])(Object(i["a"])().mark((function e(){var t;return Object(i["a"])().wrap((function(e){while(1)switch(e.prev=e.next){case 0:if(!this.note.id){e.next=6;break}return e.next=3,this.$swal(B);case 3:if(t=e.sent,t.value){e.next=6;break}return e.abrupt("return");case 6:this.$emit("saveEditedNote",this.newNote);case 7:case"end":return e.stop()}}),e,this)})));function t(){return e.apply(this,arguments)}return t}()},{key:"discardChanges",value:function(){this.$emit("discardChanges")}}]),n}(h["d"]);Object(f["a"])([Object(h["b"])({required:!0})],G.prototype,"note",void 0),Object(f["a"])([Object(h["b"])({type:Boolean,default:function(){return!1}})],G.prototype,"autoFocus",void 0),Object(f["a"])([Object(h["b"])({type:Boolean,default:function(){return!1}})],G.prototype,"confirmEdit",void 0),Object(f["a"])([Object(h["e"])("newNote",{deep:!0})],G.prototype,"onPropertyChanged",null),G=Object(f["a"])([h["a"]],G);var L=G,$=L,M=(n("295a"),Object(C["a"])($,R,A,!1,null,null,null)),H=M.exports,z=function(){var e=this,t=e._self._c;e._self._setupProxy;return t("ul",{staticClass:"list-group"},[e._l(e.notes,(function(n){return[t(e.noteComponent,{key:n.id,tag:"Component",staticClass:"list-group-item",attrs:{noteInfo:n},on:{deleteClick:e.deleteClick,editClick:e.editClick}})]}))],2)},J=[],V=function(){var e=this,t=e._self._c;e._self._setupProxy;return t("li",{staticClass:"note"},[t("p",[e._v(" "+e._s(e.noteInfo.content)+" ")]),t("div",{staticClass:"note-controls"},[e._t("default",null,{note:e.noteInfo})],2)])},q=[];function W(e,t){return!!e.$slots[t]}var Z=function(e){Object(b["a"])(n,e);var t=Object(p["a"])(n);function n(){var e;Object(l["a"])(this,n);for(var a=arguments.length,o=new Array(a),r=0;r<a;r++)o[r]=arguments[r];return e=t.call.apply(t,[this].concat(o)),Object(v["a"])(Object(d["a"])(e),"noteInfo",void 0),e}return Object(u["a"])(n,[{key:"hasSlot",value:function(e){return W(this,e)}}]),n}(h["d"]);Object(f["a"])([Object(h["b"])()],Z.prototype,"noteInfo",void 0),Z=Object(f["a"])([h["a"]],Z);var K=Z,X=K,Q=(n("94cb"),Object(C["a"])(X,V,q,!1,null,"bd4b0980",null)),Y=Q.exports,ee=function(){var e=this,t=e._self._c;e._self._setupProxy;return t("li",{staticClass:"note"},[t("p",[e._v(" "+e._s(e.noteInfo.content)+" ")]),t("div",{staticClass:"note-controls"},[t("EditActions",{on:{deleteClick:e.deleteClick,editClick:e.editClick}})],1)])},te=[],ne=function(){var e=this,t=e._self._c;e._self._setupProxy;return t("div",{staticClass:"edit-actions"},[t("span",{staticClass:"glyphicon glyphicon-pencil note-contol-icon text-muted",attrs:{"aria-hidden":"true"},on:{click:e.editClick}}),t("span",{staticClass:"glyphicon glyphicon-trash note-contol-icon text-danger",attrs:{"aria-hidden":"true"},on:{click:e.deleteClick}})])},ae=[],oe=function(e){Object(b["a"])(n,e);var t=Object(p["a"])(n);function n(){return Object(l["a"])(this,n),t.apply(this,arguments)}return Object(u["a"])(n,[{key:"deleteClick",value:function(){var e=Object(c["a"])(Object(i["a"])().mark((function e(){return Object(i["a"])().wrap((function(e){while(1)switch(e.prev=e.next){case 0:this.$emit("deleteClick");case 1:case"end":return e.stop()}}),e,this)})));function t(){return e.apply(this,arguments)}return t}()},{key:"editClick",value:function(){this.$emit("editClick")}}]),n}(h["d"]);oe=Object(f["a"])([h["a"]],oe);var re=oe,ie=re,ce=Object(C["a"])(ie,ne,ae,!1,null,null,null),se=ce.exports,le=function(e){Object(b["a"])(n,e);var t=Object(p["a"])(n);function n(){var e;Object(l["a"])(this,n);for(var a=arguments.length,o=new Array(a),r=0;r<a;r++)o[r]=arguments[r];return e=t.call.apply(t,[this].concat(o)),Object(v["a"])(Object(d["a"])(e),"noteInfo",void 0),e}return Object(u["a"])(n,[{key:"editClick",value:function(){this.$emit("editClick",this.noteInfo)}},{key:"deleteClick",value:function(){this.$emit("deleteClick",this.noteInfo)}}]),n}(h["d"]);Object(f["a"])([Object(h["b"])()],le.prototype,"noteInfo",void 0),le=Object(f["a"])([Object(h["a"])({components:{EditActions:se}})],le);var ue=le,de=ue,be=(n("f65f"),Object(C["a"])(de,ee,te,!1,null,"53ef4ace",null)),pe=be.exports,ve=function(e){Object(b["a"])(n,e);var t=Object(p["a"])(n);function n(){var e;Object(l["a"])(this,n);for(var a=arguments.length,o=new Array(a),r=0;r<a;r++)o[r]=arguments[r];return e=t.call.apply(t,[this].concat(o)),Object(v["a"])(Object(d["a"])(e),"notes",void 0),Object(v["a"])(Object(d["a"])(e),"editable",void 0),Object(v["a"])(Object(d["a"])(e),"confirmDelete",void 0),e}return Object(u["a"])(n,[{key:"noteComponent",get:function(){return this.editable?pe:Y}},{key:"editClick",value:function(e){this.$emit("editClick",e)}},{key:"deleteClick",value:function(){var e=Object(c["a"])(Object(i["a"])().mark((function e(t){var n;return Object(i["a"])().wrap((function(e){while(1)switch(e.prev=e.next){case 0:if(!this.confirmDelete){e.next=6;break}return e.next=3,this.$swal(F);case 3:if(n=e.sent,n.value){e.next=6;break}return e.abrupt("return");case 6:this.$emit("deleteClick",t);case 7:case"end":return e.stop()}}),e,this)})));function t(t){return e.apply(this,arguments)}return t}()}]),n}(h["d"]);Object(f["a"])([Object(h["b"])()],ve.prototype,"notes",void 0),Object(f["a"])([Object(h["b"])({default:function(){return!1},type:Boolean})],ve.prototype,"editable",void 0),Object(f["a"])([Object(h["b"])({default:function(){return!1},type:Boolean})],ve.prototype,"confirmDelete",void 0),ve=Object(f["a"])([Object(h["a"])({components:{Note:Y,EditableNote:pe,EditActions:se}})],ve);var fe=ve,he=fe,Oe=Object(C["a"])(he,z,J,!1,null,"5114204b",null),je=Oe.exports,ye=function(e){Object(b["a"])(n,e);var t=Object(p["a"])(n);function n(){var e;Object(l["a"])(this,n);for(var a=arguments.length,o=new Array(a),r=0;r<a;r++)o[r]=arguments[r];return e=t.call.apply(t,[this].concat(o)),Object(v["a"])(Object(d["a"])(e),"onNoteUpdate",void 0),Object(v["a"])(Object(d["a"])(e),"onNoteDelete",void 0),Object(v["a"])(Object(d["a"])(e),"onNoteAdd",void 0),Object(v["a"])(Object(d["a"])(e),"notes",void 0),Object(v["a"])(Object(d["a"])(e),"editable",void 0),Object(v["a"])(Object(d["a"])(e),"noteToEdit",null),Object(v["a"])(Object(d["a"])(e),"operation","view"),Object(v["a"])(Object(d["a"])(e),"localNotes",[]),e}return Object(u["a"])(n,[{key:"mounted",value:function(){this.localNotes=Object(s["a"])(this.notes)}},{key:"isOperation",value:function(e){return e===this.operation}},{key:"editNote",value:function(e){this.noteToEdit=e,this.operation="edit"}},{key:"addNote",value:function(){this.operation="edit",this.noteToEdit={id:null,content:""}}},{key:"deleteNote",value:function(){var e=Object(c["a"])(Object(i["a"])().mark((function e(t){var n;return Object(i["a"])().wrap((function(e){while(1)switch(e.prev=e.next){case 0:if("function"!==typeof this.onNoteUpdate){e.next=6;break}return e.next=3,this.onNoteDelete(t);case 3:e.t0=e.sent,e.next=7;break;case 6:e.t0=!0;case 7:if(n=e.t0,n){e.next=10;break}return e.abrupt("return");case 10:this.removeNoteFromList(t);case 11:case"end":return e.stop()}}),e,this)})));function t(t){return e.apply(this,arguments)}return t}()},{key:"noteChangesHandler",value:function(e){e.id?this.saveEditedNote(e):this.addAndSaveNote(e),this.noteToEdit={id:null,content:""},this.operation="view"}},{key:"addAndSaveNote",value:function(){var e=Object(c["a"])(Object(i["a"])().mark((function e(t){var n;return Object(i["a"])().wrap((function(e){while(1)switch(e.prev=e.next){case 0:if("function"!==typeof this.onNoteAdd){e.next=6;break}return e.next=3,this.onNoteAdd(t);case 3:e.t0=e.sent,e.next=7;break;case 6:e.t0=!0;case 7:if(n=e.t0,n){e.next=10;break}return e.abrupt("return");case 10:this.addNoteToList({id:n,content:t.content});case 11:case"end":return e.stop()}}),e,this)})));function t(t){return e.apply(this,arguments)}return t}()},{key:"addNoteToList",value:function(e){this.localNotes.push(e)}},{key:"saveEditedNote",value:function(){var e=Object(c["a"])(Object(i["a"])().mark((function e(t){var n;return Object(i["a"])().wrap((function(e){while(1)switch(e.prev=e.next){case 0:if("function"!==typeof this.onNoteUpdate){e.next=6;break}return e.next=3,this.onNoteUpdate(t);case 3:e.t0=e.sent,e.next=7;break;case 6:e.t0=!0;case 7:if(n=e.t0,n){e.next=10;break}return e.abrupt("return");case 10:this.updateNoteInList(t);case 11:case"end":return e.stop()}}),e,this)})));function t(t){return e.apply(this,arguments)}return t}()},{key:"updateNoteInList",value:function(e){this.localNotes=this.localNotes.map((function(t){return t.id===e.id?Object(w["a"])(Object(w["a"])({},t),{},{content:e.content}):t}))}},{key:"removeNoteFromList",value:function(e){this.localNotes=this.localNotes.filter((function(t){return t.id!==e.id}))}},{key:"discardEditChanges",value:function(){this.noteToEdit={id:null,content:""},this.operation="view"}}]),n}(h["d"]);Object(f["a"])([Object(h["b"])()],ye.prototype,"onNoteUpdate",void 0),Object(f["a"])([Object(h["b"])()],ye.prototype,"onNoteDelete",void 0),Object(f["a"])([Object(h["b"])()],ye.prototype,"onNoteAdd",void 0),Object(f["a"])([Object(h["b"])({default:function(){return[]}})],ye.prototype,"notes",void 0),Object(f["a"])([Object(h["b"])({default:function(){return!1}})],ye.prototype,"editable",void 0),ye=Object(f["a"])([Object(h["a"])({components:{NoteEditor:H,NotesList:je,BootstrapPopup:I}})],ye);var ke=ye,me=ke,Ce=(n("ecc5"),Object(C["a"])(me,E,x,!1,null,null,null)),ge=Ce.exports;function Ne(e){var t={};return e.forEach((function(e){t[e.name]=e.value})),t}var Ee=n("6f9a");function xe(){var e;window.getSelection&&(null===(e=window.getSelection())||void 0===e||e.removeAllRanges())}function we(){Object(Ee["b"])(),setTimeout((function(){Object(Ee["b"])()}),100),setTimeout((function(){Object(Ee["b"])()}),500)}var _e=function(){var e=this,t=e._self._c;e._self._setupProxy;return t("div",[t("div",{staticClass:"filter-bar"},e._l(e.usableFilterGroups,(function(n){return t("FilterManager",{key:n.filteredPropertyName,attrs:{filterGroup:n},on:{groupUpdate:e.refreshFilterGroups}})})),1),t("Calendar",{ref:"calendar",attrs:{eventSources:e.eventSources,eventRender:e.eventRender,editable:e.editable},on:{dateClick:e.emitDateClick,dateDoubleClick:e.emitDateDoubleClick,eventEdit:e.emitEventEdit,eventClick:e.eventClick}})],1)},Pe=[],De=function(){var e=this,t=e._self._c;e._self._setupProxy;return t("FullCalendar",e._b({ref:"fullCalendar",attrs:{eventRender:e.renderItem,eventSources:e.eventSources},on:{dateClick:e.handleDateClick,eventClick:e.clickEvent,eventDrop:e.handleChangeDates,eventResize:e.handleChangeDates}},"FullCalendar",e.fullCalendarProps,!1))},Se=[],Te=n("88e1"),Ue=n("f88c"),Ie=n("5739"),Re=n.n(Ie),Ae=n("19bc"),Fe=n("a7c0"),Be=(n("52df"),n("dc09").default),Ge={plugins:[Te["d"],Ue["a"],Fe["a"],Ae["a"]],header:{left:"today prev,next",center:"title",right:"dayGridMonth,timeGridWeek,dayGridDay"},timeZone:"Europe/Warsaw",eventLimit:5,defaultView:"timeGridWeek",locale:Re.a,droppable:!1,minTime:"06:00:00",maxTime:"23:00:00",businessHours:{daysOfWeek:[1,2,3,4,5],startTime:"08:00",endTime:"16:00"},showNonCurrentDates:!1,nowIndicator:!0,eventTimeFormat:{hour:"2-digit",minute:"2-digit",hour12:!1},columnHeaderFormat:{weekday:"short",month:"numeric",day:"numeric",omitCommas:!0},height:"auto",displayEventTime:!1,disableResizing:!0,eventDurationEditable:!1},Le=function(e){Object(b["a"])(n,e);var t=Object(p["a"])(n);function n(){var e;Object(l["a"])(this,n);for(var a=arguments.length,o=new Array(a),r=0;r<a;r++)o[r]=arguments[r];return e=t.call.apply(t,[this].concat(o)),Object(v["a"])(Object(d["a"])(e),"eventRender",void 0),Object(v["a"])(Object(d["a"])(e),"fullCalendar",void 0),Object(v["a"])(Object(d["a"])(e),"eventSources",void 0),Object(v["a"])(Object(d["a"])(e),"editable",void 0),Object(v["a"])(Object(d["a"])(e),"options",void 0),Object(v["a"])(Object(d["a"])(e),"tooltipOptions",void 0),Object(v["a"])(Object(d["a"])(e),"currentShowTippy",void 0),Object(v["a"])(Object(d["a"])(e),"clickCheckerId",void 0),e}return Object(u["a"])(n,[{key:"fullCalendarProps",get:function(){var e=Object.assign(Ge,this.options);return Object.assign(e,{editable:this.editable})}},{key:"renderItem",value:function(e){return!(!this.eventRender||!this.eventRender(e))&&(this.parseTooltip(e),!0)}},{key:"update",value:function(){this.fullCalendar.getApi().getEventSourceById(1).refetch()}},{key:"rerenderEvents",value:function(){this.fullCalendar.getApi().rerenderEvents()}},{key:"refeatch",value:function(e){this.fullCalendar.getApi().getEventSourceById(e).refetch()}},{key:"updateCalendarEventProp",value:function(e,t,n){e.setProp(t,n)}},{key:"findCalendarEvent",value:function(e){return this.fullCalendar.getApi().getEventById(e)}},{key:"deleteEventById",value:function(e){var t=this.findCalendarEvent(e);this.deleteEvent(t)}},{key:"deleteEvent",value:function(e){e.remove()}},{key:"createEvent",value:function(e){this.fullCalendar.getApi().addEvent(e)}},{key:"parseTooltip",value:function(e){if(e.event.extendedProps.tooltipContent){var t=Object.assign({content:e.event.extendedProps.tooltipContent},this.tooltipOptions);Object(Ee["a"])(e.el,t)}}},{key:"handleDateClick",value:function(e){var t=this;this.clickCheckerId?(this.removeClickTimeout(),this.emitExtendedDateClick(e,"double")):this.clickCheckerId=setTimeout((function(){t.removeClickTimeout(),t.emitExtendedDateClick(e,"single")}),200)}},{key:"emitExtendedDateClick",value:function(e,t){var n=this.addDayEventsToEvent(e,e.date);this.$emit("single"===t?"dateClick":"dateDoubleClick",n)}},{key:"removeClickTimeout",value:function(){clearTimeout(this.clickCheckerId),this.clickCheckerId=void 0}},{key:"addDayEventsToEvent",value:function(e,t){return Object(w["a"])(Object(w["a"])({},e),{},{dayEvents:this.getDayEvents(t)})}},{key:"getDayEvents",value:function(e){var t=this.fullCalendar.getApi(),n=t.getEvents(),a=e.toDateString();return n.filter((function(e){return e.start.toDateString()===a}))}},{key:"handleChangeDates",value:function(e){return this.editable?e.oldEvent&&e.event.allDay!==e.oldEvent.allDay?e.revert():void this.$emit("eventEdit",e):e.revert()}},{key:"clickEvent",value:function(e){var t=this.addDayEventsToEvent(e,e.event.start);this.$emit("eventClick",t)}}]),n}(h["d"]);Object(f["a"])([Object(h["b"])({type:Function})],Le.prototype,"eventRender",void 0),Object(f["a"])([Object(h["c"])()],Le.prototype,"fullCalendar",void 0),Object(f["a"])([Object(h["b"])()],Le.prototype,"eventSources",void 0),Object(f["a"])([Object(h["b"])({default:!0,type:Boolean})],Le.prototype,"editable",void 0),Object(f["a"])([Object(h["b"])({default:function(){}})],Le.prototype,"options",void 0),Object(f["a"])([Object(h["b"])({default:function(){return{onShow:function(e){var t=e.reference.classList;return!t.contains("fc-dragging")&&t.contains("fc-allow-mouse-resize")&&t.contains("fc-start")},delay:[400,0]}}})],Le.prototype,"tooltipOptions",void 0),Le=Object(f["a"])([Object(h["a"])({components:{FullCalendar:Be}})],Le);var $e=Le,Me=$e,He=(n("aa2a"),Object(C["a"])(Me,De,Se,!1,null,null,null)),ze=He.exports;function Je(e,t){var n=document.createElement("span");return n.classList.add("event-badge"),n.textContent=t,n.style.backgroundColor=e,n}var Ve=function(e){Object(b["a"])(n,e);var t=Object(p["a"])(n);function n(){var e;Object(l["a"])(this,n);for(var a=arguments.length,o=new Array(a),r=0;r<a;r++)o[r]=arguments[r];return e=t.call.apply(t,[this].concat(o)),Object(v["a"])(Object(d["a"])(e),"filterGroups",void 0),Object(v["a"])(Object(d["a"])(e),"calendar",void 0),Object(v["a"])(Object(d["a"])(e),"filterManager",void 0),Object(v["a"])(Object(d["a"])(e),"usableFilterGroups",[]),Object(v["a"])(Object(d["a"])(e),"eventSources",void 0),Object(v["a"])(Object(d["a"])(e),"notesEnabled",void 0),Object(v["a"])(Object(d["a"])(e),"editable",void 0),e}return Object(u["a"])(n,[{key:"mounted",value:function(){this.usableFilterGroups=this.filterGroups}},{key:"refreshFilterGroups",value:function(e){this.usableFilterGroups=this.usableFilterGroups.map((function(t){return t.id===e.id?e:t})),this.rerenderCalendar()}},{key:"parseEventStyles",value:function(e){var t=this;this.usableFilterGroups.forEach((function(n){n.filters.forEach((function(a){if(a.badge){var o=n.filteredPropertyName;e.event.extendedProps[o]===a.value&&t.parseBadge(a,e)}}))}))}},{key:"rerenderCalendar",value:function(){this.calendar.rerenderEvents()}},{key:"updateCalendarEventProp",value:function(e,t,n){this.calendar.updateCalendarEventProp(e,t,n)}},{key:"deleteEventById",value:function(e){return this.calendar.deleteEventById(e)}},{key:"findCalendarEvent",value:function(e){return this.calendar.findCalendarEvent(e)}},{key:"eventRender",value:function(e){return!!this.parseVisible(e)&&(this.parsePhone(e),this.parseEventStyles(e),!0)}},{key:"parseVisible",value:function(e){return this.eventShouldVisible(e.event)?(this.revealEvent(e),!0):(this.hideEvent(e),!1)}},{key:"eventShouldVisible",value:function(e){var t=this,n=!0;return this.usableFilterGroups.forEach((function(a){n=n&&t.checkIsEventVisibleInGroup(e,a)})),n}},{key:"emitDateClick",value:function(e){this.$emit("dateClick",e)}},{key:"emitDateDoubleClick",value:function(e){this.$emit("dateDoubleClick",e)}},{key:"emitEventEdit",value:function(e){this.$emit("eventEdit",e)}},{key:"checkIsEventVisibleInGroup",value:function(e,t){var n=t.filteredPropertyName;if(!(n in e.extendedProps))return!0;var a=e.extendedProps[n];return t.filters.some((function(e){return e.isActive&&e.value===a}))}},{key:"hideEvent",value:function(e){e.event.setProp("display","none")}},{key:"revealEvent",value:function(e){e.event.setProp("display","auto")}},{key:"parsePhone",value:function(e){var t=e.event.extendedProps.phone;if(t){var n=e.el.querySelector(".fc-title");n&&(n.innerHTML+="<br>"+t)}}},{key:"eventClick",value:function(e){this.$emit("eventClick",e)}},{key:"parseBadge",value:function(e,t){if(e.badge){var n=e.badge.background,a=e.badge.text;n&&a&&this.appendBadge(t,n,a)}}},{key:"appendBadge",value:function(e,t,n){e.el.appendChild(Je(t,n))}}]),n}(h["d"]);Object(f["a"])([Object(h["b"])()],Ve.prototype,"filterGroups",void 0),Object(f["a"])([Object(h["c"])()],Ve.prototype,"calendar",void 0),Object(f["a"])([Object(h["c"])()],Ve.prototype,"filterManager",void 0),Object(f["a"])([Object(h["b"])()],Ve.prototype,"eventSources",void 0),Object(f["a"])([Object(h["b"])({default:function(){return!0}})],Ve.prototype,"notesEnabled",void 0),Object(f["a"])([Object(h["b"])({default:function(){return!0}})],Ve.prototype,"editable",void 0),Ve=Object(f["a"])([Object(h["a"])({components:{Calendar:ze,FilterManager:N}})],Ve);var qe=Ve,We=qe,Ze=(n("63fb"),Object(C["a"])(We,_e,Pe,!1,null,null,null)),Ke=Ze.exports,Xe=function(e){Object(b["a"])(n,e);var t=Object(p["a"])(n);function n(){var e;Object(l["a"])(this,n);for(var a=arguments.length,o=new Array(a),r=0;r<a;r++)o[r]=arguments[r];return e=t.call.apply(t,[this].concat(o)),Object(v["a"])(Object(d["a"])(e),"filterGroups",void 0),Object(v["a"])(Object(d["a"])(e),"calendarFilter",void 0),Object(v["a"])(Object(d["a"])(e),"notesPopup",void 0),Object(v["a"])(Object(d["a"])(e),"dayNotes",[]),Object(v["a"])(Object(d["a"])(e),"notePopupTitle",""),Object(v["a"])(Object(d["a"])(e),"notePopupDate",void 0),Object(v["a"])(Object(d["a"])(e),"NoteResourceId",void 0),Object(v["a"])(Object(d["a"])(e),"allowUpdate",void 0),Object(v["a"])(Object(d["a"])(e),"extraHTTPParams",void 0),Object(v["a"])(Object(d["a"])(e),"URLAddEvent",void 0),Object(v["a"])(Object(d["a"])(e),"notesEnabled",void 0),Object(v["a"])(Object(d["a"])(e),"URLGetNotes",void 0),Object(v["a"])(Object(d["a"])(e),"URLNewNote",void 0),Object(v["a"])(Object(d["a"])(e),"URLUpdateNote",void 0),Object(v["a"])(Object(d["a"])(e),"URLDeleteNote",void 0),Object(v["a"])(Object(d["a"])(e),"eventSourcesConfig",void 0),e}return Object(u["a"])(n,[{key:"eventSources",get:function(){return[].concat(Object(s["a"])(this.mapEventSources(this.eventSourcesConfig)),[this.getNotesSettings()])}},{key:"mapEventSources",value:function(e){var t=this;return e.map((function(e){return e.extraParams=Ne(t.extraHTTPParams),e.success=t.setUpdateUrlMapper(e.urlUpdate),e}))}},{key:"setUpdateUrlMapper",value:function(e){return function(t){return t.map((function(t){return t.urlUpdate=e,t}))}}},{key:"getNotesSettings",value:function(){return this.notesEnabled?{id:this.NoteResourceId,url:this.URLGetNotes,extraParams:Ne(this.extraHTTPParams),allDayDefault:!0}:{}}},{key:"getNotesFromDayInfo",value:function(e){var t=this;return e.dayEvents.filter((function(e){return e.allDay&&t.eventIsNote(e)})).map((function(e){return{content:e.title,id:e.id}}))}},{key:"eventClick",value:function(e){this.notesEnabled&&this.eventIsNote(e.event)&&this.dateClick(e)}},{key:"eventIsNote",value:function(e){var t;return(null===e||void 0===e||null===(t=e.source)||void 0===t?void 0:t.id)==this.NoteResourceId}},{key:"dateClick",value:function(e){this.notesEnabled&&(this.dayNotes=this.getNotesFromDayInfo(e),console.log(e),console.log(e.date),console.log(e.dateStr),this.notePopupTitle="Notatki "+e.date.toLocaleDateString(),this.notePopupDate=e.date,this.notesPopup.show())}},{key:"deleteNote",value:function(){var e=Object(c["a"])(Object(i["a"])().mark((function e(t){var n,a;return Object(i["a"])().wrap((function(e){while(1)switch(e.prev=e.next){case 0:return n=new URLSearchParams,n.append("id",String(t.id)),this.extraHTTPParams.forEach((function(e){n.append(e.name,String(e.value))})),e.next=5,this.axios.post(this.URLDeleteNote,n);case 5:if(a=e.sent,200===a.status){e.next=8;break}return e.abrupt("return",!1);case 8:return this.calendarFilter.deleteEventById(t.id),e.abrupt("return",!0);case 10:case"end":return e.stop()}}),e,this)})));function t(t){return e.apply(this,arguments)}return t}()},{key:"dateDoubleClick",value:function(e){e.allDay||this.addEvent(e.date)}},{key:"addEvent",value:function(e){var t=e.toJSON();window.open("".concat(this.URLAddEvent,"?date=").concat(t))}},{key:"editNoteText",value:function(){var e=Object(c["a"])(Object(i["a"])().mark((function e(t){var n,a,o;return Object(i["a"])().wrap((function(e){while(1)switch(e.prev=e.next){case 0:return n=new URLSearchParams,n.append("news",t.content),this.extraHTTPParams.forEach((function(e){n.append(e.name,String(e.value))})),n.append("id",String(t.id)),e.next=6,this.axios.post(this.URLUpdateNote,n);case 6:if(a=e.sent,200===a.status){e.next=9;break}return e.abrupt("return",!1);case 9:if(!1!==a.data.success){e.next=11;break}return e.abrupt("return",!1);case 11:return o=this.calendarFilter.findCalendarEvent(t.id),this.calendarFilter.updateCalendarEventProp(o,"title",t.content),this.calendarFilter.rerenderCalendar(),e.abrupt("return",!0);case 15:case"end":return e.stop()}}),e,this)})));function t(t){return e.apply(this,arguments)}return t}()},{key:"addNote",value:function(){var e=Object(c["a"])(Object(i["a"])().mark((function e(t){var n,a;return Object(i["a"])().wrap((function(e){while(1)switch(e.prev=e.next){case 0:return n=new URLSearchParams,n.append("news",t.content),n.append("date",this.notePopupDate.toJSON()),this.extraHTTPParams.forEach((function(e){n.append(e.name,String(e.value))})),e.next=6,this.axios.post(this.URLNewNote,n);case 6:if(a=e.sent,200===a.status){e.next=9;break}return e.abrupt("return",!1);case 9:if(a.data.id){e.next=11;break}return e.abrupt("return",!1);case 11:return this.resourceNotes(),e.abrupt("return",a.data.id);case 13:case"end":return e.stop()}}),e,this)})));function t(t){return e.apply(this,arguments)}return t}()},{key:"resourceNotes",value:function(){this.notesEnabled&&this.calendarFilter.calendar.refeatch(this.NoteResourceId)}},{key:"updateDates",value:function(){var e=Object(c["a"])(Object(i["a"])().mark((function e(t){var n,a,o,r,c;return Object(i["a"])().wrap((function(e){while(1)switch(e.prev=e.next){case 0:return xe(),we(),n=t.event,a=this.eventIsNote(n),o=a?this.URLUpdateNote:n.extendedProps.urlUpdate,r=new URLSearchParams,r.append("id",String(n.id)),n.start&&r.append("start_at",n.start.toJSON()),n.end&&r.append("end_at",n.end.toJSON()),e.prev=9,e.next=12,this.axios.post(o,r);case 12:c=e.sent,200!==c.status&&t.revert(),e.next=19;break;case 16:e.prev=16,e.t0=e["catch"](9),t.revert();case 19:case"end":return e.stop()}}),e,this,[[9,16]])})));function t(t){return e.apply(this,arguments)}return t}()}]),n}(h["d"]);Object(f["a"])([Object(h["b"])({default:function(){return[]}})],Xe.prototype,"filterGroups",void 0),Object(f["a"])([Object(h["c"])()],Xe.prototype,"calendarFilter",void 0),Object(f["a"])([Object(h["c"])()],Xe.prototype,"notesPopup",void 0),Object(f["a"])([Object(h["b"])({default:function(){return 100}})],Xe.prototype,"NoteResourceId",void 0),Object(f["a"])([Object(h["b"])({default:function(){return!0}})],Xe.prototype,"allowUpdate",void 0),Object(f["a"])([Object(h["b"])({required:!1,default:function(){return[]}})],Xe.prototype,"extraHTTPParams",void 0),Object(f["a"])([Object(h["b"])({default:function(){return"/meet/create"}})],Xe.prototype,"URLAddEvent",void 0),Object(f["a"])([Object(h["b"])({default:function(){return!0}})],Xe.prototype,"notesEnabled",void 0),Object(f["a"])([Object(h["b"])({default:function(){return"/calendar-note/list"}})],Xe.prototype,"URLGetNotes",void 0),Object(f["a"])([Object(h["b"])({default:function(){return"/calendar-note/add"}})],Xe.prototype,"URLNewNote",void 0),Object(f["a"])([Object(h["b"])({default:function(){return"/calendar-note/update"}})],Xe.prototype,"URLUpdateNote",void 0),Object(f["a"])([Object(h["b"])({default:function(){return"/calendar-note/delete"}})],Xe.prototype,"URLDeleteNote",void 0),Object(f["a"])([Object(h["b"])({required:!0})],Xe.prototype,"eventSourcesConfig",void 0),Xe=Object(f["a"])([Object(h["a"])({components:{BootstrapPopup:I,CalendarNotes:ge,FilterManager:N,FilterCalendar:Ke}})],Xe);var Qe=Xe,Ye=Qe,et=Object(C["a"])(Ye,o,r,!1,null,null,null),tt=et.exports,nt=n("bc3a"),at=n.n(nt),ot=n("a7fe"),rt=n.n(ot),it=n("5886");n("4413");function ct(){var e=document.querySelector("meta[name=csrf-token]");return e?e.content:""}function st(e){e.axios.interceptors.response.use((function(e){return e}),(function(){return lt(e),{}}))}function lt(e){e.swal({icon:"error",title:"Ups...",text:"coś poszło nie tak!"})}a["a"].use(it["a"]),a["a"].use(rt.a,at.a),a["a"].axios.defaults.headers.common["X-CSRF-TOKEN"]=ct(),a["a"].config.productionTip=!1,st(a["a"]);var ut=document.getElementById("app");if(ut){var dt=a["a"].extend(tt),bt=ut.dataset.props?JSON.parse(ut.dataset.props):{};new dt({el:ut,propsData:bt})}},e80f:function(e,t,n){},e85a:function(e,t,n){},ecc5:function(e,t,n){"use strict";n("1c3c")},f65f:function(e,t,n){"use strict";n("c195")}});
//# sourceMappingURL=app.js.map