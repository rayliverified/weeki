package com.softdev.weekimessenger.Layouts;
import android.app.ProgressDialog;
import android.content.Intent;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import com.softdev.weekimessenger.Handlers.AppHandler;
import com.softdev.weekimessenger.Handlers.DatabaseHandler;
import com.softdev.weekimessenger.Handlers.FriendListAdapter;
import com.softdev.weekimessenger.R;
import com.softdev.weekimessenger.User;

import java.util.ArrayList;

public class Friends extends Fragment {

    FriendListAdapter fAdapter;
    ArrayList<User> friendList;
    DatabaseHandler dbHandler;
    LinearLayoutManager layoutManager;

    TextView txtNF;
    RecyclerView listView;

    public Friends() {}
    public static Friends newInstance() { return new Friends(); }

    public void Refresh() { LoadFriends(); }

    void LoadFriends() {
        ProgressDialog pDialog = new ProgressDialog(getActivity());
        pDialog.setProgressStyle(ProgressDialog.STYLE_SPINNER);
        pDialog.setMessage("Loading friend list...");
        pDialog.setIndeterminate(true);
        pDialog.setCanceledOnTouchOutside(false);
        pDialog.setCancelable(false);
        pDialog.show();
        ArrayList friends = dbHandler.getAllUsers();
        if (!friends.equals(friendList)) {
            friendList.clear();
            for (int i = 0; i < friends.size(); i++) {
                User u = (User) friends.get(i);
                if (!dbHandler.isBlocked(u.getUsername())) {
                    friendList.add(u);
                }
            }
        }
        pDialog.dismiss();
        fAdapter.notifyDataSetChanged();
        if (friendList.size() != 0) {
            txtNF.setVisibility(View.GONE);
            listView.setVisibility(View.VISIBLE);
        } else {
            txtNF.setVisibility(View.VISIBLE);
            listView.setVisibility(View.GONE);
        }
    }

    void LoadUsersData() {

    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        View v = inflater.inflate(R.layout.fragment_friends, container, false);
        friendList = new ArrayList<>();
        layoutManager = new LinearLayoutManager(getContext());
        dbHandler = AppHandler.getInstance().getDBHandler();
        txtNF = (TextView) v.findViewById(R.id.txtError);
        listView = (RecyclerView) v.findViewById(R.id.friendView);
        fAdapter = new FriendListAdapter(getContext(), friendList, new FriendListAdapter.ClickListener() {
            @Override
            public void onMessageButtonClick(View view, int position) {
                final User u = friendList.get(position);
                Intent intent = new Intent(getContext(), Chatbox.class);
                intent.putExtra("isGroup", "0");
                intent.putExtra("username", u.getUsername());
                startActivity(intent);
            }
        });

        listView.setLayoutManager(layoutManager);
        listView.addItemDecoration(new ItemsDivider(getContext()));
        listView.setAdapter(fAdapter);

        // Load List of friends in your database
        LoadFriends();
        return v;
    }
}
