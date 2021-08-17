(window.webpackJsonp=window.webpackJsonp||[]).push([[406],{108:function(t,e,r){t.exports=r(336)},1301:function(t,e,r){"use strict";r.r(e);var a=r(129),n=r.n(a),i=r(108),s=r.n(i),o=r(80),c=r.n(o),u=r(72),l=r.n(u),d=r(81),f=r.n(d),v=r(47),m=r.n(v),p=r(23),h=r.n(p),_=r(34),g=r.n(_),b=r(39),x=r.n(b),y=r(56),k=r.n(y),S=r(1307),w=r(494),C=r(1319),T=r(1404);function I(t,e){var r=m()(t);if(f.a){var a=f()(t);e&&(a=a.filter((function(e){return l()(t,e).enumerable}))),r.push.apply(r,a)}return r}function P(t){for(var e=1;e<arguments.length;e++){var r=null!=arguments[e]?arguments[e]:{};e%2?I(Object(r),!0).forEach((function(e){x()(t,e,r[e])})):c.a?s()(t,c()(r)):I(Object(r)).forEach((function(e){n()(t,e,l()(r,e))}))}return t}var O=[{title:"用户名",dataIndex:"nickname",ellipsis:!0,scopedSlots:{customRender:"nickname"}},{title:"现带班课总数",dataIndex:"liveMultiClassNum",ellipsis:!0},{title:"现学员总数",dataIndex:"liveMultiClassStudentNum",ellipsis:!0},{title:"已结班班课总数",dataIndex:"endMultiClassNum",ellipsis:!0},{title:"已结班班课学员总数",dataIndex:"endMultiClassStudentNum",ellipsis:!0},{title:"是否推荐",scopedSlots:{customRender:"promoteInfo"}},{title:"最近登录",scopedSlots:{customRender:"loginInfo"}},{title:"操作",scopedSlots:{customRender:"action"}}],j={name:"Teachers",components:{userInfoTable:C.a,AsideLayout:S.a,AAvatar:T.a},data:function(){return{visible:!1,user:{},columns:O,pageData:[],loading:!1,pagination:{},keyWord:"",setNumId:0,modalVisible:!1,form:this.$form.createForm(this,{name:"set_number"})}},created:function(){this.fetchTeacher()},methods:{handleTableChange:function(t){var e=P({},this.pagination);e.current=t.current,this.pagination=e;var r={limit:t.pageSize,offset:(t.current-1)*t.pageSize};this.fetchTeacher(r)},fetchTeacher:function(t){var e=this;return g()(h.a.mark((function r(){var a,n,i,s;return h.a.wrap((function(r){for(;;)switch(r.prev=r.next){case 0:return e.loading=!0,r.next=3,w.q.search(P({limit:20,nickname:e.keyWord},t));case 3:a=r.sent,n=a.data,i=a.paging,(s=P({},e.pagination)).total=i.total,s.pageSize=Number(i.limit),k.a.forEach(n,(function(t){t.isPromoted=1==t.promoted})),e.loading=!1,e.pageData=n,e.pagination=!(i.total<Number(i.limit))&&s;case 13:case"end":return r.stop()}}),r)})))()},onSearch:function(t){var e=this;return g()(h.a.mark((function r(){return h.a.wrap((function(r){for(;;)switch(r.prev=r.next){case 0:e.keyWord=t,e.pagination.current=1,e.fetchTeacher();case 3:case"end":return r.stop()}}),r)})))()},edit:function(t){var e=this;return g()(h.a.mark((function r(){return h.a.wrap((function(r){for(;;)switch(r.prev=r.next){case 0:return r.next=2,w.s.get(t);case 2:e.user=r.sent,e.visible=!0;case 4:case"end":return r.stop()}}),r)})))()},close:function(){this.visible=!1},clickSetNumberModal:function(t){this.setNumId=t,this.modalVisible=!0},handleOk:function(t){var e=this;this.form.validateFields(function(){var t=g()(h.a.mark((function t(r,a){var n;return h.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:if(r){t.next=6;break}return t.next=3,w.q.promotion(e.setNumId,a);case 3:n=t.sent,n.success&&(k.a.forEach(e.pageData,(function(t){if(t.id==e.setNumId)return t.promotedSeq=a.number,!1})),e.handleCancel());case 6:case"end":return t.stop()}}),t)})));return function(e,r){return t.apply(this,arguments)}}())},handleCancel:function(){this.modalVisible=!1,this.form.resetFields()},changePromoted:function(t,e){var r=this;return g()(h.a.mark((function a(){var n;return h.a.wrap((function(a){for(;;)switch(a.prev=a.next){case 0:if(n={},!t){a.next=7;break}return a.next=4,w.q.promotion(e);case 4:return n=a.sent,r.changePromotedCallBack(n,e,t),a.abrupt("return");case 7:r.$confirm({content:"真的要取消该教师推荐吗？",okText:"确认",cancelText:"取消",onOk:function(){var a=g()(h.a.mark((function a(){return h.a.wrap((function(a){for(;;)switch(a.prev=a.next){case 0:return a.next=2,w.q.cancelPromotion(e);case 2:n=a.sent,r.changePromotedCallBack(n,e,t);case 4:case"end":return a.stop()}}),a)})));return function(){return a.apply(this,arguments)}}()});case 8:case"end":return a.stop()}}),a)})))()},changePromotedCallBack:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{},e=arguments.length>1?arguments[1]:void 0,r=arguments.length>2?arguments[2]:void 0;t.success&&k.a.forEach(this.pageData,(function(t){if(t.id==e)return t.isPromoted=r,!1}))},validateRange:function(t,e,r){!1===k.a.inRange(e,0,10001)&&r("请输入0-10000的整数"),r()}}},$=(r(1403),r(31)),N=Object($.a)(j,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("aside-layout",{staticClass:"teacher-manage-container",attrs:{breadcrumbs:[{name:"教师管理"}]}},[r("div",{staticClass:"clearfix cd-mb24"},[r("a-input-search",{staticStyle:{width:"224px"},attrs:{placeholder:"请输入用户名搜索",allowClear:!0},on:{search:t.onSearch}})],1),t._v(" "),r("a-table",{attrs:{columns:t.columns,"data-source":t.pageData,"row-key":function(t){return t.id},pagination:t.pagination,"row-class-name":function(t){return"teacher-manage-row"},loading:t.loading},on:{change:t.handleTableChange},scopedSlots:t._u([{key:"nickname",fn:function(e,a){return[r("a-avatar",{attrs:{size:48,src:a.mediumAvatar,icon:"user"}}),t._v(" "),r("a",{staticStyle:{"margin-left":"8px"},on:{click:function(e){return t.edit(t.item.id)}}},[t._v(t._s(e))])]}},{key:"promoteInfo",fn:function(e){return r("div",{},[r("a-checkbox",{attrs:{checked:e.isPromoted},on:{change:function(r){return t.changePromoted(r.target.checked,e.id)}}}),t._v(" "),e.isPromoted?r("span",{staticClass:"color-gray text-sm"},[t._v(t._s(e.promotedSeq))]):t._e(),t._v(" "),e.isPromoted?r("a",{staticClass:"set-number",attrs:{href:"javascript:;"},on:{click:function(r){return t.clickSetNumberModal(e.id)}}},[t._v("序号设置")]):t._e()],1)}},{key:"loginInfo",fn:function(e){return r("div",{},[r("div",[t._v(t._s(t.$dateFormat(e.loginTime,"YYYY-MM-DD HH:mm")))]),t._v(" "),r("div",{staticClass:"color-gray text-sm"},[t._v(t._s(e.loginIp))])])}},{key:"action",fn:function(e){return[r("a-button",{attrs:{type:"link"},on:{click:function(r){return t.edit(e.id)}}},[t._v("\n        查看\n      ")]),t._v(" "),r("a-dropdown",[r("a",{staticClass:"ant-dropdown-link",staticStyle:{"margin-left":"-6px"},on:{click:function(t){t.preventDefault()}}},[r("a-icon",{attrs:{type:"caret-down"}})],1),t._v(" "),r("a-menu",{attrs:{slot:"overlay"},slot:"overlay"},[r("a-menu-item",[r("a",{attrs:{"data-toggle":"modal","data-target":"#modal","data-backdrop":"static","data-keyboard":"false","data-url":"/admin/v2/user/"+e.id+"/edit"}},[t._v("\n              编辑用户信息\n            ")])]),t._v(" "),r("a-menu-item",[r("a",{attrs:{"data-toggle":"modal","data-target":"#modal","data-backdrop":"static","data-keyboard":"false","data-url":"/admin/v2/user/"+e.id+"/avatar"}},[t._v("\n              修改用户头像\n            ")])])],1)],1)]}}])}),t._v(" "),r("a-modal",{attrs:{title:"教师详细信息",visible:t.visible},on:{cancel:t.close}},[r("userInfoTable",{attrs:{user:t.user}}),t._v(" "),r("template",{slot:"footer"},[r("a-button",{key:"back",on:{click:t.close}},[t._v(" 关闭 ")])],1)],2),t._v(" "),r("a-modal",{attrs:{title:"设置推荐教师",okText:"确认",cancelText:"取消",visible:t.modalVisible},on:{ok:t.handleOk,cancel:t.handleCancel}},[r("a-form",{attrs:{form:t.form,"label-col":{span:3},"wrapper-col":{span:21}}},[r("a-form-item",{attrs:{label:"序号",extra:"请输入0-10000的整数"}},[r("a-input-number",{directives:[{name:"decorator",rawName:"v-decorator",value:["number",{rules:[{required:!0,message:"请输入序号"},{type:"integer",message:"请输入整数"},{validator:t.validateRange,message:"请输入0-10000的整数"}]}],expression:"['number', { rules: [\n            { required: true, message: '请输入序号' },\n            { type: 'integer', message: '请输入整数' },\n            { validator: validateRange, message: '请输入0-10000的整数' },\n          ]}]"}],staticStyle:{width:"100%"}})],1)],1)],1)],1)}),[],!1,null,"ef1150d0",null);e.default=N.exports},1305:function(t,e,r){},1307:function(t,e,r){"use strict";var a={name:"AsideLayout",props:{breadcrumbs:{type:Array,required:!0},headerTitle:{type:String,default:""},headerTip:{type:String,default:""}}},n=(r(1308),r(31)),i=Object(n.a)(a,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("div",{staticClass:"aside-layout"},[r("div",{staticClass:"aside-layout-header"},[r("a-breadcrumb",{staticClass:"pull-left aside-layout-header__breadcrumb",attrs:{separator:"/"}},t._l(t.breadcrumbs,(function(e,a){return r("a-breadcrumb-item",{key:a},[e.href?[r("a",{attrs:{href:e.href,target:"_blank"}},[t._v(t._s(e.name))])]:e.pathName?[r("a",{attrs:{href:"javascript:;"},on:{click:function(r){return t.$router.push({name:e.pathName})}}},[t._v(t._s(e.name))])]:[t._v("\n          "+t._s(e.name)+"\n        ")]],2)})),1),t._v(" "),t.headerTip?r("a-popover",{attrs:{placement:"bottomLeft"}},[r("template",{slot:"content"},[r("div",{staticClass:"aside-header-tip",domProps:{innerHTML:t._s(t.headerTip)}})]),t._v(" "),r("span",{staticClass:"aside-header-title-icon"},[r("a-icon",{attrs:{theme:"filled",type:"question-circle"}}),r("span",{staticClass:"icon-circle"},[t._v(t._s(t.headerTitle))])],1)],2):t._e()],1),t._v(" "),r("div",{staticClass:"aside-layout-main"},[t._t("default")],2)])}),[],!1,null,null,null);e.a=i.exports},1308:function(t,e,r){"use strict";var a=r(1305);r.n(a).a},1319:function(t,e,r){"use strict";var a={props:{user:{type:Object,default:{}}},computed:{getUserRoles:function(){return _.join(this.user.user.roles," ")}},methods:{toPersonalHomepage:function(t){window.open("/user/"+t+"/about","_blank")},formatTimeIp:function(t,e){var r="";return r+=0!=t?this.$dateFormat(t,"YYYY-MM-DD HH:mm"):" -- ",""!=e&&(r+=" / "+e+" 本机IP"),r},formatStr:function(t){return void 0===t||""==t||null==t?"暂无":t},formatGender:function(){return{male:"男性",female:"女性",secret:"秘密"}[this.user.profile.gender]}}},n=r(31),i=Object(n.a)(a,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("table",{staticClass:"table table-striped table-condenseda table-bordered"},[r("tbody",[r("tr",[r("th",{attrs:{width:"30%"}},[t._v("用户名")]),t._v(" "),r("td",{attrs:{width:"70%"}},[r("a",{staticClass:"pull-right",attrs:{href:"javascript:;"},on:{click:function(e){return t.toPersonalHomepage(t.user.user.id)}}},[t._v("个人主页")]),t._v("\n        "+t._s(t.user.user.nickname)+"\n      ")])]),t._v(" "),r("tr",[r("th",[t._v("Email")]),t._v(" "),r("td",[t._v(t._s(t.user.profile.email||"- -"))])]),t._v(" "),r("tr",[r("th",[t._v("用户组")]),t._v(" "),r("td",[t._v(t._s(t.getUserRoles))])]),t._v(" "),r("tr",[r("th",[t._v("注册时间/IP")]),t._v(" "),r("td",[t._v(t._s(t.formatTimeIp(t.user.user.createdTime,t.user.user.createdIp)))])]),t._v(" "),r("tr",[r("th",[t._v("最近登录时间/IP")]),t._v(" "),r("td",[t._v(t._s(t.formatTimeIp(t.user.user.loginTime,t.user.user.loginIp)))])]),t._v(" "),r("tr",[r("th",[t._v("姓名")]),t._v(" "),r("td",[t._v(t._s(t.formatStr(t.user.profile.truename)))])]),t._v(" "),r("tr",[r("th",[t._v("性别")]),t._v(" "),r("td",[t._v(t._s(t.formatGender()))])]),t._v(" "),r("tr",[r("th",[t._v("身份证号")]),t._v(" "),r("td",[t._v(t._s(t.formatStr(t.user.profile.idcard)))])]),t._v(" "),r("tr",[r("th",[t._v("手机号码")]),t._v(" "),r("td",[t._v(t._s(t.formatStr(t.user.profile.mobile)))])]),t._v(" "),r("tr",[r("th",[t._v("公司")]),t._v(" "),r("td",[t._v(t._s(t.formatStr(t.user.profile.company)))])]),t._v(" "),r("tr",[r("th",[t._v("职业")]),t._v(" "),r("td",[t._v(t._s(t.formatStr(t.user.profile.job)))])]),t._v(" "),r("tr",[r("th",[t._v("头衔")]),t._v(" "),r("td",[t._v(t._s(t.formatStr(t.user.user.title)))])]),t._v(" "),r("tr",[r("th",[t._v("个人签名")]),t._v(" "),r("td",[t._v(t._s(t.formatStr(t.user.profile.signature)))])]),t._v(" "),r("tr",[r("th",[t._v("自我介绍")]),t._v(" "),r("td",{domProps:{innerHTML:t._s(t.user.profile.about||"暂无")}})]),t._v(" "),r("tr",[r("th",[t._v("个人网站")]),t._v(" "),r("td",[t._v(t._s(t.formatStr(t.user.profile.site)))])]),t._v(" "),r("tr",[r("th",[t._v("微博")]),t._v(" "),r("td",[t._v(t._s(t.formatStr(t.user.profile.weibo)))])]),t._v(" "),r("tr",[r("th",[t._v("微信")]),t._v(" "),r("td",[t._v(t._s(t.formatStr(t.user.profile.weixin)))])]),t._v(" "),r("tr",[r("th",[t._v("QQ")]),t._v(" "),r("td",[t._v(t._s(t.formatStr(t.user.profile.qq)))])])])])}),[],!1,null,null,null);e.a=i.exports},1349:function(t,e,r){},1403:function(t,e,r){"use strict";var a=r(1349);r.n(a).a},1404:function(t,e,r){"use strict";var a=r(16),n=r.n(a),i=r(4),s=r.n(i),o=r(7),c=r.n(o),u=r(1312),l=r(1320),d=r(1309),f=r(1306),v={name:"AAvatar",props:{prefixCls:{type:String,default:void 0},shape:{validator:function(t){return["circle","square"].includes(t)},default:"circle"},size:{validator:function(t){return"number"==typeof t||["small","large","default"].includes(t)},default:"default"},src:String,srcSet:String,icon:f.a.any,alt:String,loadError:Function},inject:{configProvider:{default:function(){return u.a}}},data:function(){return{isImgExist:!0,isMounted:!1,scale:1}},watch:{src:function(){var t=this;this.$nextTick((function(){t.isImgExist=!0,t.scale=1,t.$forceUpdate()}))}},mounted:function(){var t=this;this.$nextTick((function(){t.setScale(),t.isMounted=!0}))},updated:function(){var t=this;this.$nextTick((function(){t.setScale()}))},methods:{setScale:function(){if(this.$refs.avatarChildren&&this.$refs.avatarNode){var t=this.$refs.avatarChildren.offsetWidth,e=this.$refs.avatarNode.offsetWidth;0===t||0===e||this.lastChildrenWidth===t&&this.lastNodeWidth===e||(this.lastChildrenWidth=t,this.lastNodeWidth=e,this.scale=e-8<t?(e-8)/t:1)}},handleImgLoadError:function(){var t=this.$props.loadError;!1!==(t?t():void 0)&&(this.isImgExist=!1)}},render:function(){var t,e,r=arguments[0],a=this.$props,i=a.prefixCls,o=a.shape,u=a.size,f=a.src,v=a.alt,m=a.srcSet,p=Object(d.b)(this,"icon"),h=this.configProvider.getPrefixCls,_=h("avatar",i),g=this.$data,b=g.isImgExist,x=g.scale,y=g.isMounted,k=(t={},c()(t,_+"-lg","large"===u),c()(t,_+"-sm","small"===u),t),S=s()(c()({},_,!0),k,(e={},c()(e,_+"-"+o,o),c()(e,_+"-image",f&&b),c()(e,_+"-icon",p),e)),w="number"==typeof u?{width:u+"px",height:u+"px",lineHeight:u+"px",fontSize:p?u/2+"px":"18px"}:{},C=this.$slots.default;if(f&&b)C=r("img",{attrs:{src:f,srcSet:m,alt:v},on:{error:this.handleImgLoadError}});else if(p)C="string"==typeof p?r(l.a,{attrs:{type:p}}):p;else{var T=this.$refs.avatarChildren;if(T||1!==x){var I="scale("+x+") translateX(-50%)",P={msTransform:I,WebkitTransform:I,transform:I},O="number"==typeof u?{lineHeight:u+"px"}:{};C=r("span",{class:_+"-string",ref:"avatarChildren",style:s()({},O,P)},[C])}else{var j={};y||(j.opacity=0),C=r("span",{class:_+"-string",ref:"avatarChildren",style:{opacity:0}},[C])}}return r("span",n()([{ref:"avatarNode"},{on:Object(d.c)(this),class:S,style:w}]),[C])}},m=r(1313);v.install=function(t){t.use(m.a),t.component(v.name,v)};e.a=v},210:function(t,e,r){r(211);var a=r(49).Object;t.exports=function(t,e){return a.getOwnPropertyDescriptor(t,e)}},211:function(t,e,r){var a=r(134),n=r(180).f;r(335)("getOwnPropertyDescriptor",(function(){return function(t,e){return n(a(t),e)}}))},335:function(t,e,r){var a=r(74),n=r(49),i=r(309);t.exports=function(t,e){var r=(n.Object||{})[t]||Object[t],s={};s[t]=e(r),a(a.S+a.F*i((function(){r(1)})),"Object",s)}},336:function(t,e,r){r(337);var a=r(49).Object;t.exports=function(t,e){return a.defineProperties(t,e)}},337:function(t,e,r){var a=r(74);a(a.S+a.F*!r(140),"Object",{defineProperties:r(379)})},338:function(t,e,r){r(339),t.exports=r(49).Object.getOwnPropertyDescriptors},339:function(t,e,r){var a=r(74),n=r(340),i=r(134),s=r(180),o=r(381);a(a.S,"Object",{getOwnPropertyDescriptors:function(t){for(var e,r,a=i(t),c=s.f,u=n(a),l={},d=0;u.length>d;)void 0!==(r=c(a,e=u[d++]))&&o(l,e,r);return l}})},340:function(t,e,r){var a=r(358),n=r(342),i=r(133),s=r(102).Reflect;t.exports=s&&s.ownKeys||function(t){var e=a.f(i(t)),r=n.f;return r?e.concat(r(t)):e}},341:function(t,e,r){r(380),t.exports=r(49).Object.getOwnPropertySymbols},39:function(t,e,r){var a=r(129);t.exports=function(t,e,r){return e in t?a(t,e,{value:r,enumerable:!0,configurable:!0,writable:!0}):t[e]=r,t}},47:function(t,e,r){t.exports=r(581)},581:function(t,e,r){r(582),t.exports=r(49).Object.keys},582:function(t,e,r){var a=r(357),n=r(356);r(335)("keys",(function(){return function(t){return n(a(t))}}))},72:function(t,e,r){t.exports=r(210)},80:function(t,e,r){t.exports=r(338)},81:function(t,e,r){t.exports=r(341)}}]);