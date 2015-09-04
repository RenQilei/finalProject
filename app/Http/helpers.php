<?php

use App\Department;

function isManageableDepartment($managedDepartment, $currentDepartment)
{
    if($managedDepartment == $currentDepartment) {
        return true;
    }
    else {
        $parentDepartment = parentDepartment($currentDepartment);

        if ($parentDepartment) {
            if ($parentDepartment == $managedDepartment) {
                return true;
            } else {
                if ($parentDepartment != $currentDepartment) {
                    return isManageableDepartment($managedDepartment, $parentDepartment);
                }
            }
        } else {
            return false;
        }
    }
}

function parentDepartment($currentDepartment)
{
    $department = Department::find($currentDepartment);

    return $department ? ($department->parent_department ? $department->parent_department : $currentDepartment) : null;
}