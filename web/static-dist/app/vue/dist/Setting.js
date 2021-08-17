(window.webpackJsonp=window.webpackJsonp||[]).push([[405],{1300:function(e,t,a){"use strict";a.r(t);var r=a(23),i=a.n(r),s=a(34),n=a.n(s),o=a(494),l={name:"index",components:{AsideLayout:a(1307).a},data:function(){return{rules:{group_number_limit:[{required:!0,message:"请输入分组学员人数上限",trigger:"blur"},{validator:this.validatorGroupNumber,trigger:"blur"}],assistant_service_limit:[{required:!0,message:"请输入助教服务学员人数上限",trigger:"blur"},{validator:this.validatorAssistantService,trigger:"blur"}],review_time_limit:[{required:!0,message:"请输入超时未批阅时间设定",trigger:"blur"},{validator:this.validatorReviewTime,trigger:"blur"}]},form:{group_number_limit:"",assistant_service_limit:"",review_time_limit:""},ajaxLoading:!1}},computed:{},mounted:function(){this.getParams()},methods:{getParams:function(){var e=this;return n()(i.a.mark((function t(){return i.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return t.next=2,o.m.search();case 2:e.form=t.sent,console.log(e.form);case 4:case"end":return t.stop()}}),t)})))()},validatorGroupNumber:function(e,t,a){(t>1e4||0==t)&&a("人数范围在1-10000人"),!1===/^\+?[1-9][0-9]*$/.test(t)&&a("请输入正整数"),a()},validatorAssistantService:function(e,t,a){(t>1e4||0==t)&&a("人数范围在1-10000人"),!1===/^\+?[1-9][0-9]*$/.test(t)&&a("请输入正整数"),a()},validatorReviewTime:function(e,t,a){t>200&&a("时间范围在0-200小时"),!1===/^\+?[0-9][0-9]*$/.test(t)&&a("请输入正整数"),a()},handleSubmit:function(){var e=this;this.$refs.form.validate().then(n()(i.a.mark((function t(){return i.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return e.ajaxLoading=!0,t.prev=1,t.next=4,o.m.add(e.form);case 4:e.$message.success("保存成功");case 5:return t.prev=5,e.ajaxLoading=!1,t.finish(5);case 8:case"end":return t.stop()}}),t,null,[[1,,5,8]])}))))}}},u=(a(1402),a(31)),m=Object(u.a)(l,(function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("aside-layout",{attrs:{breadcrumbs:[{name:"参数设置"}]}},[a("a-form-model",{ref:"form",staticStyle:{"max-width":"500px"},attrs:{model:e.form,rules:e.rules,"label-col":{span:10},"wrapper-col":{span:10}}},[a("a-form-model-item",{ref:"group_number_limit",attrs:{label:"分组学员人数上限",prop:"group_number_limit"}},[a("a-input",{model:{value:e.form.group_number_limit,callback:function(t){e.$set(e.form,"group_number_limit",t)},expression:"form.group_number_limit"}},[a("span",{attrs:{slot:"suffix"},slot:"suffix"},[e._v("人")])])],1),e._v(" "),a("a-form-model-item",{ref:"assistant_service_limit",attrs:{label:"助教服务学员人数上限",prop:"assistant_service_limit"}},[a("a-input",{model:{value:e.form.assistant_service_limit,callback:function(t){e.$set(e.form,"assistant_service_limit",t)},expression:"form.assistant_service_limit"}},[a("span",{attrs:{slot:"suffix"},slot:"suffix"},[e._v("人")])])],1),e._v(" "),a("a-form-model-item",{ref:"review_time_limit",attrs:{label:"超时未批阅时间设定",prop:"review_time_limit"}},[a("a-input",{model:{value:e.form.review_time_limit,callback:function(t){e.$set(e.form,"review_time_limit",t)},expression:"form.review_time_limit"}},[a("span",{attrs:{slot:"suffix"},slot:"suffix"},[e._v("小时")])])],1)],1),e._v(" "),a("p",{staticClass:"setup-tip"},[e._v("此处为全局设置，将应用到默认分组大班课，大班课和分组大班课内设置可覆盖全局设置")]),e._v(" "),a("div",{staticClass:"setup-btn"},[a("a-space",{attrs:{size:"large"}},[a("a-button",{attrs:{type:"primary",loading:e.ajaxLoading},on:{click:e.handleSubmit}},[e._v("\n        提交\n      ")])],1)],1)],1)}),[],!1,null,"58dbf4b1",null);t.default=m.exports},1305:function(e,t,a){},1307:function(e,t,a){"use strict";var r={name:"AsideLayout",props:{breadcrumbs:{type:Array,required:!0},headerTitle:{type:String,default:""},headerTip:{type:String,default:""}}},i=(a(1308),a(31)),s=Object(i.a)(r,(function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",{staticClass:"aside-layout"},[a("div",{staticClass:"aside-layout-header"},[a("a-breadcrumb",{staticClass:"pull-left aside-layout-header__breadcrumb",attrs:{separator:"/"}},e._l(e.breadcrumbs,(function(t,r){return a("a-breadcrumb-item",{key:r},[t.href?[a("a",{attrs:{href:t.href,target:"_blank"}},[e._v(e._s(t.name))])]:t.pathName?[a("a",{attrs:{href:"javascript:;"},on:{click:function(a){return e.$router.push({name:t.pathName})}}},[e._v(e._s(t.name))])]:[e._v("\n          "+e._s(t.name)+"\n        ")]],2)})),1),e._v(" "),e.headerTip?a("a-popover",{attrs:{placement:"bottomLeft"}},[a("template",{slot:"content"},[a("div",{staticClass:"aside-header-tip",domProps:{innerHTML:e._s(e.headerTip)}})]),e._v(" "),a("span",{staticClass:"aside-header-title-icon"},[a("a-icon",{attrs:{theme:"filled",type:"question-circle"}}),a("span",{staticClass:"icon-circle"},[e._v(e._s(e.headerTitle))])],1)],2):e._e()],1),e._v(" "),a("div",{staticClass:"aside-layout-main"},[e._t("default")],2)])}),[],!1,null,null,null);t.a=s.exports},1308:function(e,t,a){"use strict";var r=a(1305);a.n(r).a},1348:function(e,t,a){},1402:function(e,t,a){"use strict";var r=a(1348);a.n(r).a}}]);