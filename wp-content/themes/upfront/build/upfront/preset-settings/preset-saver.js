!function(e){upfrontrjs.define([],function(){var n=function(){var n={},t=function(e,n,r){var i=n.pop();Upfront.Util.post({action:"upfront_save_"+e+"_preset",data:i}).done(function(){n.length>0?t(e,n,r):r.resolve()}).fail(function(){r.reject(),Upfront.Views.Editor.notify("Preset "+i.name+" save failed.")})};this.save=function(){var r=[],i=e.Deferred();return _.each(n,function(n,i){if(n.length>0){var o=e.Deferred();r.push(o),t(i,n,o)}}),r.length>0?(e.when.apply(e,r).done(function(){i.resolve()}).fail(function(){i.reject()}),i):i.resolve()},this.queue=function(e,t){n[t]=n[t]||[],n[t]=_.reject(n[t],function(n){return n.id===e.id}),n[t].push(e)}},t=new n;return t})}(jQuery);