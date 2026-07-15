import { useNavigate, useParams } from 'react-router-dom';
import { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useFormSubmit } from '../../hooks/useFormSubmit';
import { roleService } from '../../services/roleService';
import { permissionService } from '../../services/permissionService';
import { PageHeader } from '../../components/ui/PageHeader';
import { Spinner } from '../../components/ui/Spinner';

const schema = z.object({
  name: z.string().min(1, 'Name wajib diisi').max(50, 'Maksimal 50 karakter'),
  display_name: z.string().min(1, 'Display Name wajib diisi').max(100, 'Maksimal 100 karakter'),
  description: z.string().optional().or(z.literal('')),
  is_active: z.boolean().optional().default(true),
  permission_ids: z.array(z.number()).optional().default([]),
});

export function RoleFormPage() {
  const { id } = useParams();
  const isEdit = !!id;
  const navigate = useNavigate();
  const [loadingData, setLoadingData] = useState(isEdit);
  const [permissions, setPermissions] = useState([]);
  const [loadingPerms, setLoadingPerms] = useState(true);

  const form = useFormSubmit({
    service: roleService,
    method: isEdit ? 'update' : 'create',
    id,
    queryKey: 'roles',
    onSuccess: () => navigate('/roles'),
  });

  const {
    register,
    handleSubmit,
    formState: { errors },
    reset,
    setValue,
    watch,
  } = useForm({
    resolver: zodResolver(schema),
    defaultValues: {
      is_active: true,
      permission_ids: [],
    },
  });

  const selectedPermIds = watch('permission_ids') || [];

  // Load all permissions for the checklist
  useEffect(() => {
    permissionService
      .getAllUnpaginated()
      .then((res) => {
        const items = res.data || res || [];
        setPermissions(items);
      })
      .finally(() => setLoadingPerms(false));
  }, []);

  // Load existing data for edit
  useEffect(() => {
    if (isEdit) {
      roleService
        .getById(id)
        .then((res) => {
          const data = res.data || res;
          const permIds = (data.permissions || []).map((p) => p.id);
          reset({
            name: data.name || '',
            display_name: data.display_name || '',
            description: data.description || '',
            is_active: data.is_active ?? true,
            permission_ids: permIds,
          });
        })
        .finally(() => setLoadingData(false));
    }
  }, [id, isEdit, reset]);

  const togglePermission = (permId) => {
    const current = [...selectedPermIds];
    const idx = current.indexOf(permId);
    if (idx >= 0) {
      current.splice(idx, 1);
    } else {
      current.push(permId);
    }
    setValue('permission_ids', current, { shouldValidate: true });
  };

  const onSubmit = async (data) => {
    await form.submit({
      ...data,
      permission_ids: data.permission_ids,
    });
  };

  // Group permissions by module
  const groupedPermissions = permissions.reduce((acc, perm) => {
    const module = perm.module || 'Other';
    if (!acc[module]) acc[module] = [];
    acc[module].push(perm);
    return acc;
  }, {});

  if (loadingData) {
    return (
      <div className="flex items-center justify-center py-20">
        <Spinner size="lg" />
      </div>
    );
  }

  return (
    <div>
      <PageHeader
        title={isEdit ? 'Edit Role' : 'Tambah Role'}
        description={isEdit ? 'Perbarui data peran' : 'Buat peran baru'}
      />

      <form onSubmit={handleSubmit(onSubmit)} className="max-w-2xl space-y-5">
        {/* Name */}
        <div>
          <label className="mb-1.5 block text-sm font-medium text-slate-300">
            Name <span className="text-red-400">*</span>
          </label>
          <input
            {...register('name')}
            placeholder="Nama role (contoh: admin)"
            className="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
          />
          {errors.name && (
            <p className="mt-1 text-xs text-red-400">{errors.name.message}</p>
          )}
          {form.errors?.name && (
            <p className="mt-1 text-xs text-red-400">{form.errors.name}</p>
          )}
        </div>

        {/* Display Name */}
        <div>
          <label className="mb-1.5 block text-sm font-medium text-slate-300">
            Display Name <span className="text-red-400">*</span>
          </label>
          <input
            {...register('display_name')}
            placeholder="Nama tampilan role"
            className="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
          />
          {errors.display_name && (
            <p className="mt-1 text-xs text-red-400">{errors.display_name.message}</p>
          )}
          {form.errors?.display_name && (
            <p className="mt-1 text-xs text-red-400">{form.errors.display_name}</p>
          )}
        </div>

        {/* Description */}
        <div>
          <label className="mb-1.5 block text-sm font-medium text-slate-300">Description</label>
          <textarea
            {...register('description')}
            rows={3}
            placeholder="Deskripsi role"
            className="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
          />
          {errors.description && (
            <p className="mt-1 text-xs text-red-400">{errors.description.message}</p>
          )}
          {form.errors?.description && (
            <p className="mt-1 text-xs text-red-400">{form.errors.description}</p>
          )}
        </div>

        {/* Is Active */}
        <div>
          <label className="flex items-center gap-3">
            <input
              type="checkbox"
              {...register('is_active')}
              className="h-4 w-4 rounded border-slate-700 bg-slate-800 text-cyan-500 focus:ring-cyan-500"
            />
            <span className="text-sm font-medium text-slate-300">Active</span>
          </label>
        </div>

        {/* Permission Checklist (grouped by module) */}
        <div>
          <label className="mb-3 block text-sm font-medium text-slate-300">
            Permissions <span className="text-red-400">*</span>
          </label>
          {loadingPerms ? (
            <div className="flex items-center gap-2 text-sm text-slate-500">
              <Spinner size="sm" /> Memuat permissions...
            </div>
          ) : (
            <div className="space-y-4">
              {Object.entries(groupedPermissions).map(([module, perms]) => (
                <div key={module}>
                  <h4 className="mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
                    {module}
                  </h4>
                  <div className="grid grid-cols-2 gap-2 rounded-lg border border-slate-700 bg-slate-800/50 p-3">
                    {perms.map((perm) => (
                      <label
                        key={perm.id}
                        className="flex cursor-pointer items-center gap-2 rounded px-2 py-1.5 transition hover:bg-slate-700"
                      >
                        <input
                          type="checkbox"
                          checked={selectedPermIds.includes(perm.id)}
                          onChange={() => togglePermission(perm.id)}
                          className="h-4 w-4 rounded border-slate-600 bg-slate-700 text-cyan-500 focus:ring-cyan-500"
                        />
                        <span className="text-sm text-slate-300">
                          {perm.display_name || perm.name}
                        </span>
                      </label>
                    ))}
                  </div>
                </div>
              ))}
            </div>
          )}
          {errors.permission_ids && (
            <p className="mt-1 text-xs text-red-400">{errors.permission_ids.message}</p>
          )}
        </div>

        {/* Actions */}
        <div className="flex items-center gap-3 pt-2">
          <button
            type="submit"
            disabled={form.loading}
            className="inline-flex items-center gap-2 rounded-lg bg-cyan-500 px-6 py-2.5 text-sm font-medium text-white transition hover:bg-cyan-600 disabled:cursor-not-allowed disabled:opacity-50"
          >
            {form.loading ? (
              <>
                <Spinner size="sm" />
                Menyimpan...
              </>
            ) : (
              'Simpan'
            )}
          </button>
          <button
            type="button"
            onClick={() => navigate('/roles')}
            className="rounded-lg border border-slate-700 px-6 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-slate-800"
          >
            Batal
          </button>
        </div>
      </form>
    </div>
  );
}
