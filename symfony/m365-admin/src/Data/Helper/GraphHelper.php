<?php

namespace App\Data\Helper;

class GraphHelper
{
    const GROUPS_DATATYPE = '#microsoft.graph.group';

    public function extractGroupIds(array $userGroups): array
    {
        $groupIds = [];

        if (isset($userGroups) && !empty(is_array($userGroups))) {
            foreach ($userGroups as $group) {
                if ($group->getodataType() === self::GROUPS_DATATYPE) {
                    if ($group->getid()) {
                        $groupIds[] = $group->getid();
                    }
                }
            }
        }

        return $groupIds;
    }

    public function extractGroupNames(array $userGroups): array
    {
        $groupNames = [];

        if (isset($userGroups) && !empty(is_array($userGroups))) {
            foreach ($userGroups as $group) {
                if ($group->getodataType() === self::GROUPS_DATATYPE) {
                    if ($group->getdisplayName()) {
                        $groupNames[] = $group->getdisplayName();
                    }
                }
            }
        }

        return $groupNames;
    }

    public function extractGroupNamesWithIds(array $groups): array
    {
        $groupIdsWItchNames = [];

        if (isset($groups) && !empty(is_array($groups))) {
            foreach ($groups as $group) {
                if ($group->getodataType() === self::GROUPS_DATATYPE) {
                    if ($group->getdisplayName() && $group->getid()) {
                        $groupIdsWItchNames[] = $group->getdisplayName() . ' - ' . $group->getid() . '';
                    }
                }
            }
        }

        return $groupIdsWItchNames;
    }

    public function extractGroupMembersNames(array $groupMembers): array
    {
        $groupMembersNames = [];

        if (isset($groupMembers) && !empty(is_array($groupMembers))) {
            foreach ($groupMembers as $member) {
                if ($member->getdisplayName()) {
                    $groupMembersNames[] = $member->getdisplayName();
                }
            }
        }

        return $groupMembersNames;
    }
    public function userExists($users, $email): bool|string
    {
        foreach ($users as $user) {
            if (strtolower($user->getUserPrincipalName()) === strtolower($email) || strtolower($user->getMail()) === strtolower($email)) {
                return  $user->getId();
            }
        }

        return false;
    }

    public function groupExists($groups, $groupName): bool|string
    {
        foreach ($groups as $group) {
            if (strtolower($group->getDisplayName()) === strtolower($groupName) ) {
                return $group->getId();
            }
        }

        return false;
    }
}