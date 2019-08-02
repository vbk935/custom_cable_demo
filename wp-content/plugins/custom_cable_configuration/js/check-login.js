function checkLogin(url, formData, successCallback, errorCallback, beforeSendCallback) {
	if (formData === undefined)
		formData={};
	if (errorCallback === undefined) {
		errorCallback = function(){ return true; };
	}
	if (beforeSendCallback === undefined) {
		beforeSendCallback = function(){ return true; }
	}
	jQuery.ajax({
	    url: url,
	    data: formData,
	    method: 'post',
	    datatype: 'json',
	    beforeSend: function () {
    		showLoader(true);
    		beforeSendCallback();
	    },
	    success: function (response) {
	    	showLoader(false);
	    	if (response.login == 1)
	    		return successCallback();
	    	return errorCallback();
	    },
	    error: function(e) {
	    	showLoader(false);
	    	// console.log("Error", e);
	    	return errorCallback(e);
	    }
	});
}