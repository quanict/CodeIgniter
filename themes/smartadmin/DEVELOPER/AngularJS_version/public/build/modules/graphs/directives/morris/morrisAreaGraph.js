define(["modules/graphs/module","morris"],function(a){"use strict";return a.registerDirective("morrisAreaGraph",function(){return{restrict:"E",replace:!0,template:'<div class="chart no-padding"></div>',link:function(a,b){Morris.Area({element:b,data:[{x:"2011 Q1",y:3,z:3},{x:"2011 Q2",y:2,z:0},{x:"2011 Q3",y:0,z:2},{x:"2011 Q4",y:4,z:4}],xkey:"x",ykeys:["y","z"],labels:["Y","Z"]})}}})});