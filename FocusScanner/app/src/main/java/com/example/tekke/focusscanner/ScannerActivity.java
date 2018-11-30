package com.example.tekke.focusscanner;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.ImageButton;
import android.widget.ImageView;
import android.widget.TextView;

import com.google.zxing.integration.android.IntentIntegrator;
import com.google.zxing.integration.android.IntentResult;

public class ScannerActivity extends Activity {

    private ImageButton scanner;
    private ImageView image;
    private TextView text;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_scanner);
        this.image = new ImageView(ScannerActivity.this);
        this.scanner = (ImageButton) findViewById(R.id.scanner);
        this.text = (TextView) findViewById(R.id.text);
        final Activity activity = this;
        scanner.setOnClickListener(new View.OnClickListener()
        {
            @Override
            public void onClick(View view)
            {
                IntentIntegrator integrator = new IntentIntegrator(activity);
                integrator.setDesiredBarcodeFormats(IntentIntegrator.QR_CODE_TYPES);
                integrator.setPrompt("Scan");
                integrator.setCameraId(0);
                integrator.setBeepEnabled(false);
                integrator.setBarcodeImageEnabled(false);
                integrator.initiateScan();

            }
        });
    }
    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data)
    {
        IntentResult result = IntentIntegrator.parseActivityResult(requestCode, resultCode, data);
        if (result!=null)
        {
            if(result.getContents()!=null) {
                //Toast.makeText(this, result.getContents(), Toast.LENGTH_LONG).show();
                String code = result.getContents();
                String type = "search";
                BackgroundWorker backgroundWorker = new BackgroundWorker(this);
                backgroundWorker.execute(type, code);
             /*   if (backgroundWorker.imageResult() == "1")
                {
                    scanner.setBackgroundResource(R.drawable.green);

                }
                else if (backgroundWorker.imageResult() == "2")
                {
                    scanner.setImageResource(R.drawable.red);
                }
                */
                //code contains qr
                //send code to database
            }
        }
        else
        {
            super.onActivityResult(requestCode, resultCode, data);
        }
    }
}
