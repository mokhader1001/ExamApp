<?php

namespace App\Controllers;

use App\Models\SettingsModel;

class Settings extends BaseController
{
    public function index()
    {
        if (session()->get('role') !== 'teacher')
            return redirect()->to('/login');

        $settingsModel = new SettingsModel();
        $data['settings'] = $settingsModel->findAll();
        return view('teacher/settings', $data);
    }

    public function save()
    {
        if (session()->get('role') !== 'teacher')
            return redirect()->to('/login');

        $settingsModel = new SettingsModel();
        $settings = $this->request->getPost('settings'); // Expecting array: key => value

        foreach ($settings as $key => $value) {
            $settingsModel->where('key', $key)->set(['value' => $value])->update();
        }

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
