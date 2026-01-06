<?php

namespace App\Controllers;

use App\Models\UserModel;

class Profile extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $data['user'] = $userModel->find(session()->get('user_id'));

        $view = session()->get('role') === 'teacher' ? 'teacher/profile' : 'student/profile';
        return view($view, $data);
    }

    public function updateInfo()
    {
        $userModel = new UserModel();
        $id = session()->get('user_id');

        $rules = [
            'full_name' => 'required|min_length[3]|max_length[100]',
            'username' => "required|min_length[3]|max_length[50]|is_unique[users.username,id,{$id}]"
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validation failed: ' . implode(', ', $this->validator->getErrors()));
        }

        $data = [
            'full_name' => $this->request->getPost('full_name'),
            'username' => $this->request->getPost('username')
        ];

        if ($userModel->update($id, $data)) {
            session()->set('full_name', $data['full_name']);
            session()->set('username', $data['username']);
            return redirect()->back()->with('success', 'Profile information updated successfully.');
        }

        return redirect()->back()->with('error', 'Failed to update profile.');
    }

    public function updateSecurity()
    {
        $userModel = new UserModel();
        $id = session()->get('user_id');
        $user = $userModel->find($id);

        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        // Verify current password
        if (!password_verify($currentPassword, $user['password'])) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        // Validate new password
        if ($newPassword !== $confirmPassword) {
            return redirect()->back()->with('error', 'New passwords do not match.');
        }

        if (strlen($newPassword) < 6) {
            return redirect()->back()->with('error', 'New password must be at least 6 characters long.');
        }

        if ($userModel->update($id, ['password' => $newPassword])) {
            return redirect()->back()->with('success', 'Password updated successfully.');
        }

        return redirect()->back()->with('error', 'Failed to update password.');
    }
}
