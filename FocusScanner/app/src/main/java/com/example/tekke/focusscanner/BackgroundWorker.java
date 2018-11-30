package com.example.tekke.focusscanner;

import android.app.AlertDialog;
import android.content.Context;
import android.os.AsyncTask;
import android.widget.ImageView;
import android.text.Html;

import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLEncoder;
import java.util.ArrayList;
import java.util.List;

/**
 * Created by tekke on 7/24/2017.
 */
public class BackgroundWorker extends AsyncTask<String,Void,String> {
    Context context;
    AlertDialog alertDialog;
    private String ImageID;
    private Boolean Check = false;

    BackgroundWorker(Context ctxt) {
        context = ctxt;
    }

    @Override
    protected String doInBackground(String... params) {
        String type = params[0];
        String search_url = "http://focus-oc.com/tickets/searchQR.php";
        //String search_url = "http://192.168.50.66/Focus/searchQR.php";
        if (type.equals("search")) {
            try {
                String ticketID = params[1];
                URL url = new URL(search_url);
                HttpURLConnection httpURLConnection = (HttpURLConnection) url.openConnection();
                httpURLConnection.setRequestMethod("POST");
                httpURLConnection.setDoOutput(true);
                httpURLConnection.setDoInput(true);
                OutputStream outputStream = httpURLConnection.getOutputStream();
                BufferedWriter bufferedWriter = new BufferedWriter(new OutputStreamWriter(outputStream, "UTF-8"));
                String post_data = URLEncoder.encode("ticketID", "UTF-8") + "=" + URLEncoder.encode(ticketID, "UTF-8");
                bufferedWriter.write(post_data);
                bufferedWriter.flush();
                bufferedWriter.close();
                outputStream.close();
                InputStream inputStream = httpURLConnection.getInputStream();
                BufferedReader bufferedReader = new BufferedReader(new InputStreamReader(inputStream, "iso-8859-1"));

                String result = "";
                String line = "";

                while ((line = bufferedReader.readLine()) != null) {
                    result += (line);
                }
                bufferedReader.close();
                inputStream.close();
                httpURLConnection.disconnect();
                return result;

            } catch (MalformedURLException e) {
                e.printStackTrace();
            } catch (IOException e) {
                e.printStackTrace();
            }
        }
        return null;
    }

    @Override
    protected void onPreExecute() {
        alertDialog = new AlertDialog.Builder(context).create();
        alertDialog.setTitle("Search Status");
    }
    @Override
    protected void onPostExecute(String result)
    {
       String output = "";
       for (String message: result.split("@"))
        {
            output+=message+"\r\n";
        }
        alertDialog.setMessage(output);
        alertDialog.show();

    }
    @Override
    protected void onProgressUpdate(Void...values)
    {
        super.onProgressUpdate(values);
    }
    /*protected String imageResult()
    {
        return ImageID;
    }*/
}
