<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int|null $created_by
 * @property int|null $room_id
 * @property string|null $location_detail
 * @property string $name_asset
 * @property int|null $asset_category_id
 * @property string $asset_type
 * @property string|null $serial_number
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $purchase_date
 * @property string|null $condition
 * @property string $status
 * @property int $current_stock
 * @property int $minimum_stock
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AssetCategory|null $category
 * @property-read \App\Models\User|null $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssetsMaintenance> $maintenances
 * @property-read int|null $maintenances_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssetMovement> $movements
 * @property-read int|null $movements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PackingList> $packingLists
 * @property-read int|null $packing_lists_count
 * @property-read \App\Models\Room|null $room
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $tasks
 * @property-read int|null $tasks_count
 * @property-read \App\Models\User|null $updater
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereAssetCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereAssetType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereCurrentStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereLocationDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereMinimumStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereNameAsset($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset wherePurchaseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereSerialNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereUpdatedBy($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAsset {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Asset> $assets
 * @property-read int|null $assets_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAssetCategory {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $asset_id
 * @property int|null $from_room_id
 * @property int|null $to_room_id
 * @property int $moved_by_user_id
 * @property \Illuminate\Support\Carbon $movement_time
 * @property int|null $task_id
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Asset $asset
 * @property-read \App\Models\Room|null $fromRoom
 * @property-read \App\Models\User $movedBy
 * @property-read \App\Models\Task|null $task
 * @property-read \App\Models\Room|null $toRoom
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMovement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMovement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMovement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMovement whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMovement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMovement whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMovement whereFromRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMovement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMovement whereMovedByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMovement whereMovementTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMovement whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMovement whereToRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMovement whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAssetMovement {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $asset_id
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property string|null $maintenance_type
 * @property string $description
 * @property string|null $notes
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Asset $asset
 * @property-read \App\Models\Task|null $task
 * @property-read \App\Models\User|null $technician
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetsMaintenance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetsMaintenance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetsMaintenance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetsMaintenance whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetsMaintenance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetsMaintenance whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetsMaintenance whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetsMaintenance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetsMaintenance whereMaintenanceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetsMaintenance whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetsMaintenance whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetsMaintenance whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetsMaintenance whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetsMaintenance whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAssetsMaintenance {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name_building
 * @property string|null $address
 * @property float|null $lat_building
 * @property float|null $long_building
 * @property int|null $created_by
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Floor> $floors
 * @property-read int|null $floors_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Building newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Building newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Building query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Building whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Building whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Building whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Building whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Building whereLatBuilding($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Building whereLongBuilding($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Building whereNameBuilding($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Building whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Building whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperBuilding {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $reporter_name Nama pelapor, bisa tamu atau staff
 * @property string $location_text Deskripsi lokasi, cth: Lobi dekat pintu barat
 * @property string $status
 * @property int|null $room_id
 * @property int|null $asset_id
 * @property int|null $created_by
 * @property int|null $task_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Asset|null $asset
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\Room|null $room
 * @property-read \App\Models\Task|null $task
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereLocationText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereReporterName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperComplaint {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $building_id
 * @property string $name_floor
 * @property int|null $created_by
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Building $building
 * @property-read \App\Models\User|null $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Room> $rooms
 * @property-read int|null $rooms_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Floor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Floor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Floor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Floor whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Floor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Floor whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Floor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Floor whereNameFloor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Floor whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Floor whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperFloor {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $document_number
 * @property string $recipient_name
 * @property int|null $created_by
 * @property string|null $notes
 * @property string|null $signature_pad
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Asset> $assets
 * @property-read int|null $assets_count
 * @property-read \App\Models\User|null $creator
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackingList newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackingList newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackingList query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackingList whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackingList whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackingList whereDocumentNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackingList whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackingList whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackingList whereRecipientName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackingList whereSignaturePad($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackingList whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPackingList {}
}

namespace App\Models{
/**
 * @property string $role_id
 * @property string $role_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereRoleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperRole {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $floor_id
 * @property string $name_room
 * @property int|null $created_by
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\Floor $floor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereFloorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereNameRoom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperRoom {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $task_type_id
 * @property string $priority
 * @property int|null $user_id
 * @property int|null $asset_id
 * @property int|null $assets_maintenance_id
 * @property int|null $room_id
 * @property string $title
 * @property string|null $description
 * @property string|null $report_text
 * @property string|null $status
 * @property string|null $rejection_notes
 * @property int|null $reviewed_by
 * @property string|null $review_notes
 * @property \Illuminate\Support\Carbon|null $due_date
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $image_before
 * @property string|null $image_after
 * @property-read \App\Models\Asset|null $asset
 * @property-read \App\Models\User|null $assignee
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\AssetsMaintenance|null $maintenanceRecord
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TaskReportHistory> $reportHistories
 * @property-read int|null $report_histories_count
 * @property-read \App\Models\Room|null $room
 * @property-read \App\Models\TaskType|null $taskType
 * @method static \Database\Factories\TaskFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereAssetsMaintenanceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereImageAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereImageBefore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereRejectionNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereReportText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereReviewNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereReviewedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereTaskTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTask {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $task_id
 * @property int $submitted_by
 * @property string $report_text
 * @property string|null $image_before
 * @property string|null $image_after
 * @property \Illuminate\Support\Carbon $submitted_at
 * @property string $review_action
 * @property string|null $review_notes
 * @property int|null $reviewed_by
 * @property \Illuminate\Support\Carbon|null $reviewed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $reviewedBy
 * @property-read \App\Models\User $submittedBy
 * @property-read \App\Models\Task $task
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskReportHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskReportHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskReportHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskReportHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskReportHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskReportHistory whereImageAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskReportHistory whereImageBefore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskReportHistory whereReportText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskReportHistory whereReviewAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskReportHistory whereReviewNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskReportHistory whereReviewedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskReportHistory whereReviewedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskReportHistory whereSubmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskReportHistory whereSubmittedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskReportHistory whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskReportHistory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTaskReportHistory {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name_task
 * @property string|null $description
 * @property string|null $notification_template
 * @property string|null $departemen
 * @property string $priority_level
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskType whereDepartemen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskType whereNameTask($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskType whereNotificationTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskType wherePriorityLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTaskType {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $profile_picture
 * @property string|null $telegram_chat_id
 * @property string|null $signature_image
 * @property string|null $role_id
 * @property string $status
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $createdTasks
 * @property-read int|null $created_tasks_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Role|null $role
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $tasks
 * @property-read int|null $tasks_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePicture($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSignatureImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTelegramChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

