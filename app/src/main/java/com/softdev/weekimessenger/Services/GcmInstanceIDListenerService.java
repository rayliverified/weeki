package com.softdev.weekimessenger.Services;

import android.content.Intent;

import com.google.android.gms.iid.InstanceIDListenerService;

public class GcmInstanceIDListenerService extends InstanceIDListenerService {

    // Raises when GCM ID is changed.
    @Override
    public void onTokenRefresh() {
        Intent intent = new Intent(this, GCMService.class);
        startService(intent);
    }


}
