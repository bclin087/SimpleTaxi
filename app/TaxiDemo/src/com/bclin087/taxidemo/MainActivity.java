package com.bclin087.taxidemo;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

public class MainActivity extends Activity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        SharedPreferences prefs = this.getSharedPreferences("com.bclin087.taxidemo", Context.MODE_PRIVATE);
        String api_url = prefs.getString("api_url", Constants.API_URL_BASE);
        ((EditText)findViewById(R.id.editTextApiUrl)).setText(api_url);
    }
    
    public void login_driver(View view) {
    	TextView tvMobile = (TextView)findViewById(R.id.editTextAddress);
    	String mobileString = tvMobile.getText().toString();
    	if(mobileString.length() > 0) {
    		saveAPIURLPref();
        	Intent intent = new Intent(MainActivity.this, DriverActivity.class);
        	Bundle b = new Bundle();
        	b.putString("driver_id", mobileString);
        	intent.putExtras(b); 
        	startActivity(intent);
    	}
    	else {
    		Toast.makeText(this, R.string.input_mobile, Toast.LENGTH_SHORT).show();
    	}
    }
    
    public void login_user(View view) {
    	TextView tvMobile = (TextView)findViewById(R.id.editTextAddress);
    	String mobileString = tvMobile.getText().toString();
    	if(mobileString.length() > 0) {
    		saveAPIURLPref();
        	Intent intent = new Intent(MainActivity.this, UserActivity.class);
        	Bundle b = new Bundle();
        	b.putString("mobile_phone", mobileString);
        	intent.putExtras(b); 
        	startActivity(intent);
    	}
    	else {
    		Toast.makeText(this, R.string.input_mobile, Toast.LENGTH_SHORT).show();
    	}
    }
    
    void saveAPIURLPref() {
    	SharedPreferences prefs = this.getSharedPreferences("com.bclin087.taxidemo", Context.MODE_PRIVATE);
    	prefs.edit().putString("api_url", ((EditText)findViewById(R.id.editTextApiUrl)).getText().toString());
    }
}
