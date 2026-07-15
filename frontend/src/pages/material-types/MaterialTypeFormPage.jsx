import { useNavigate, useParams } from 'react-router-dom';
import { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useFormSubmit } from '../../hooks/useFormSubmit';
import { materialTypeService } from '../../services/materialTypeService';
import { PageHeader } from '../../components/ui/PageHeader';
import { Spinner } from '../../components/ui/Spinner';

const schema = z.object({
  name: z.string().min(1, 'Nama wajib diisi').max(100, 'Maksimal 100 karakter'),
  description: z.string().optional().or(z.literal('')),
  is_active: z.boolean().optional().default(true),
});

export function MaterialTypeFormPage() {
  const { id } = useParams();
  const isEdit = !!id;
  const navigate = useNavigate();
  const [loadingData, setLoadingData] = useState(isEdit);

  const form = useFormSubmit({
    service: materialTypeService,
    method: isEdit ? 'update' : 'create',
    id,
    queryKey: 'material-types',
    onSuccess: () => navigate('/material-types'),
  });

  const {
    register,
    handleSubmit,
    formState: { errors },
    reset,
  } = useForm({
    resolver: zodResolver(schema),
    defaultValues: {
      is_active: true,
    },
  });

  useEffect(() => {
    if (isEdit) {
      materialTypeService
        .getById(id)
        .then((res) => {
          const data = res.data || res;
          reset({
            name: data.name || '',
            description: data.description || '',
            is_active: data.is_active ?? true,
          });
        })
        .finally(() => setLoadingData(false));
    }
  }, [id, isEdit, reset]);

  const onSubmit = async (data) => {
    await form.submit(data);
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
        title={isEdit ? 'Edit Material Type' : 'Tambah Material Type'}
        description={isEdit ? 'Perbarui data tipe material' : 'Buat tipe material baru'}
      />

      <form onSubmit={handleSubmit(onSubmit)} className="max-w-2xl space-y-5">
        {/* Name */}
        <div>
          <label className="mb-1.5 block text-sm font-medium text-slate-300">
            Name <span className="text-red-400">*</span>
          </label>
          <input
            {...register('name')}
            placeholder="Nama tipe material"
            className="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
          />
          {errors.name && (
            <p className="mt-1 text-xs text-red-400">{errors.name.message}</p>
          )}
          {form.errors?.name && (
            <p className="mt-1 text-xs text-red-400">{form.errors.name}</p>
          )}
        </div>

        {/* Description */}
        <div>
          <label className="mb-1.5 block text-sm font-medium text-slate-300">Description</label>
          <textarea
            {...register('description')}
            rows={3}
            placeholder="Deskripsi tipe material"
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
            onClick={() => navigate('/material-types')}
            className="rounded-lg border border-slate-700 px-6 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-slate-800"
          >
            Batal
          </button>
        </div>
      </form>
    </div>
  );
}
