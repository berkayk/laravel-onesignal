<input type="hidden" name="OneSignalUserId" id="OneSignalUserId">
<script>
    OneSignal.isPushNotificationsEnabled(function(isEnabled) {
    if (isEnabled) {
        // user has subscribed
        OneSignal.getUserId( function(userId) {
            var OneSignalUserId=document.getElementById('OneSignalUserId');
            OneSignalUserId.innerText=userId;     
        });
    }else{
        var OneSignalUserId=document.getElementById('OneSignalUserId');
            OneSignalUserId.innerText=null;
    }
    });
</script>