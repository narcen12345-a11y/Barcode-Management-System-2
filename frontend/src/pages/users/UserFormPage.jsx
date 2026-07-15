import { useNavigate, useParams } from 'react-router-dom';
import { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useFormSubmit } from '../../hooks/useFormSubmit';
import { userService } from '../../services/userService';
import { roleService } from '../../services/roleService';
import { PageHeader } from '../../components/ui/PageHeader';
import { Spinner } from '../../components/ui/Spinner';

const schema = z.object({
  username: z.string().min(3, 'Username minimal 3 karakter').max(50, 'Maksimal 50 karakter'),
  email: z.string().email('Format email tidak valid').max(255, 'Maksimal 255 karakter'),
  password: z.string().min(6, 'Password minimal 6 karakter').max(100, 'Maksimal 100 karakter').optional().or(z.literal('')),
  password_confirmation: z.string().optional().or(z.literal('')),
  full_name: z.string().min(1, 'Nama lengkap wajib diisi').max(255, 'Maksimal 255 karakter'),
  role_ids: z.array(z.number()).optional().default([]),
}).refine(
  (data) => {
    if (data.password || data.password_confirmation) {
      return data.password === data.password_confirmation;
    }
    return true;
  },
  {
    message: 'Password confirmation tidak cocok',
    path: ['password_confirmation'],
  }
);

export function UserFormPage() {
  const { id } = useParams();
  const isEdit = !!id;
  const navigate = useNavigate();
  const [loadingData, setLoadingData] = useState(isEdit);
  const [roles, setRoles] = useState([]);
  const [loadingRoles, setLoadingRoles] = useState(true);

  const form = useFormSubmit({
    service: userService,
    method: isEdit ? 'update' : 'create',
    id,
    queryKey: 'users',
    onSuccess: () => navigate('/users'),
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
      username: '',
      email: '',
      password: '',
      password_confirmation: '',
      full_name: '',
      role_ids: [],
    },
  });

  const selectedRoleIds = watch('role_ids') || [];

  // Load all roles for multi-select
  useEffect(() => {
    roleService
      .getAllUnpaginated()
      .then((res) => {
        const items = res.data || res || [];
        setRoles(items);
      })
      .finally(() => setLoadingRoles(false));
  }, []);

  // Load existing data for edit
  useEffect(() => {
    if (isEdit) {
      userService
        .getById(id)
        .then((res) => {
          const data = res.data || res;
          const roleIds = (data.roles || []).map((r) => r.id);
          reset({
            username: data.username || '',
            email: data.email || '',
            password: '',
            password_confirmation: '',
            full_name: data.full_name || '',
            role_ids: roleIds,
          });
        })
        .finally(() => setLoadingData(false));
    }
  }, [id, isEdit, reset]);

  const toggleRole = (roleId) => {
    const current = [...selectedRoleIds];
    const idx = current.indexOf(roleId);
    if (idx >= 0) {
      current.splice(idx, 1);
    } else {
      current.push(roleId);
    }
    setValue('role_ids', current, { shouldValidate: true });
  };

  const onSubmit = async (data) => {
    const payload = {
      username: data.username,
      email: data.email,
      full_name: data.full_name,
      role_ids: data.role_ids,
    };

    // Only send password if provided (for create it's required, for edit it's optional)
    if (data.password) {
      payload.password = data.password;
      payload.password_confirmation = data.password_confirmation;
    }

    await form.submit(payload);
  };

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
        title={isEdit ? 'Edit User' : 'Tambah User'}
        description={isEdit ? 'Perbarui data pengguna' : 'Buat pengguna baru'}
      />

      <form onSubmit={handleSubmit(onSubmit)} className="max-w-2xl space-y-5">
        {/* Username */}
        <div>
          <label className="mb-1.5 block text-sm font-medium text-slate-300">
            Username <span className="text-red-400">*</span>
          </label>
          <input
            {...register('username')}
            placeholder="Username"
            className="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
          />
          {errors.username && (
            <p className="mt-1 text-xs text-red-400">{errors.username.message}</p>
          )}
          {form.errors?.username && (
            <p className="mt-1 text-xs text-red-400">{form.errors.username}</p>
          )}
        </div>

        {/* Email */}
        <div>
          <label className="mb-1.5 block text-sm font-medium text-slate-300">
            Email <span className="text-red-400">*</span>
          </label>
          <input
            type="email"
            {...register('email')}
            placeholder="email@example.com"
            className="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
          />
          {errors.email && (
            <p className="mt-1 text-xs text-red-400">{errors.email.message}</p>
          )}
          {form.errors?.email && (
            <p className="mt-1 text-xs text-red-400">{form.errors.email}</p>
          )}
        </div>

        {/* Full Name */}
        <div>
          <label className="mb-1.5 block text-sm font-medium text-slate-300">
            Full Name <span className="text-red-400">*</span>
          </label>
          <input
            {...register('full_name')}
            placeholder="Nama lengkap"
            className="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
          />
          {errors.full_name && (
            <p className="mt-1 text-xs text-red-400">{errors.full_name.message}</p>
          )}
          {form.errors?.full_name && (
            <p className="mt-1 text-xs text-red-400">{form.errors.full_name}</p>
          )}
        </div>

        {/* Password */}
        <div>
          <label className="mb-1.5 block text-sm font-medium text-slate-300">
            Password {!isEdit && <span className="text-red-400">*</span>}
            {isEdit && <span className="text-xs text-slate-500 ml-1">(kosongkan jika tidak diubah)</span>}
          </label>
          <input
            type="password"
            {...register('password')}
            placeholder={isEdit ? 'Biarkan kosong jika tidak diubah' : 'Password minimal 6 karakter'}
            className="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
          />
          {errors.password && (
            <p className="mt-1 text-xs text-red-400">{errors.password.message}</p>
          )}
          {form.errors?.password && (
            <p className="mt-1 text-xs text-red-400">{form.errors.password}</p>
          )}
        </div>

        {/* Password Confirmation */}
        <div>
          <label className="mb-1.5 block text-sm font-medium text-slate-300">
            Konfirmasi Password {!isEdit && <span className="text-red-400">*</span>}
          </label>
          <input
            type="password"
            {...register('password_confirmation')}
            placeholder="Ulangi password"
            className="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
          />
          {errors.password_confirmation && (
            <p className="mt-1 text-xs text-red-400">{errors.password_confirmation.message}</p>
          )}
        </div>

        {/* Roles Multi-select */}
        <div>
          <label className="mb-3 block text-sm font-medium text-slate-300">Roles</label>
          {loadingRoles ? (
            <div className="flex items-center gap-2 text-sm text-slate-500">
              <Spinner size="sm" /> Memuat roles...
            </div>
          ) : (
            <div className="grid grid-cols-2 gap-2 rounded-lg border border-slate-700 bg-slate-800/50 p-3">
              {roles.map((role) => (
                <label
                  key={role.id}
                  className="flex cursor-pointer items-center gap-2 rounded px-2 py-1.5 transition hover:bg-slate-700"
                >
                  <input
                    type="checkbox"
                    checked={selectedRoleIds.includes(role.id)}
                    onChange={() => toggleRole(role.id)}
                    className="h-4 w-4 rounded border-slate-600 bg-slate-700 text-cyan-500 focus:ring-cyan-500"
                  />
                  <span className="text-sm text-slate-300">
                    {role.display_name || role.name}
                  </span>
                </label>
              ))}
              {roles.length === 0 && (
                <p className="col-span-2 text-sm text-slate-500">Tidak ada role tersedia</p>
              )}
            </div>
          )}
          {errors.role_ids && (
            <p className="mt-1 text-xs text-red-400">{errors.role_ids.message}</p>
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
            onClick={() => navigate('/users')}
            className="rounded-lg border border-slate-700 px-6 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-slate-800"
          >
            Batal
          </button>
        </div>
      </form>
    </div>
  );
}
